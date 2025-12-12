<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use App\Service\PdfService;
use App\Service\FirebaseService;

class PaymentsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Payments');
    }

    public function index()
    {
        $user = $this->Authentication->getIdentity();
        
        $query = $this->Payments->find()
            ->contain(['Contracts', 'Tenants', 'Landlords']);
        
        if ($user->role === 'tenant') {
            $this->loadModel('Tenants');
            $tenant = $this->Tenants->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if ($tenant) {
                $query->where(['Payments.tenant_id' => $tenant->id]);
            }
        } elseif ($user->role === 'landlord') {
            $this->loadModel('Landlords');
            $landlord = $this->Landlords->find()
                ->where(['user_id' => $user->id])
                ->first();
            
            if ($landlord) {
                $query->where(['Payments.landlord_id' => $landlord->id]);
            }
        }
        
        $payments = $query->order(['Payments.created' => 'DESC'])->toArray();
        $this->set(compact('payments'));
    }

    public function view($id = null)
    {
        $user = $this->Authentication->getIdentity();
        
        try {
            $payment = $this->Payments->get($id, [
                'contain' => ['Contracts.Units.Properties', 'Tenants.Users', 'Landlords.Users', 'Invoices'],
            ]);
            
            // Check access permissions
            if ($user->role === 'tenant') {
                $this->loadModel('Tenants');
                $tenant = $this->Tenants->find()
                    ->where(['user_id' => $user->id])
                    ->first();
                
                if (!$tenant || $payment->tenant_id !== $tenant->id) {
                    $this->Flash->error('Access denied');
                    return $this->redirect(['action' => 'index']);
                }
            } elseif ($user->role === 'landlord') {
                $this->loadModel('Landlords');
                $landlord = $this->Landlords->find()
                    ->where(['user_id' => $user->id])
                    ->first();
                
                if (!$landlord || $payment->landlord_id !== $landlord->id) {
                    $this->Flash->error('Access denied');
                    return $this->redirect(['action' => 'index']);
                }
            }
            
            $this->set(compact('payment'));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Payment not found');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        $user = $this->Authentication->getIdentity();
        
        if ($user->role !== 'tenant') {
            $this->Flash->error('Only tenants can make payments');
            return $this->redirect(['action' => 'index']);
        }
        
        $this->loadModel('Tenants');
        $this->loadModel('Contracts');
        
        $tenant = $this->Tenants->find()
            ->where(['user_id' => $user->id])
            ->first();
        
        if (!$tenant) {
            $this->Flash->error('Tenant profile not found');
            return $this->redirect(['action' => 'index']);
        }
        
        $contracts = $this->Contracts->find()
            ->where(['tenant_id' => $tenant->id, 'status' => 'active'])
            ->contain(['Units.Properties', 'Landlords'])
            ->toArray();
        
        $payment = $this->Payments->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Get contract details
            $contract = $this->Contracts->get($data['contract_id'], [
                'contain' => ['Landlords', 'Tenants'],
            ]);
            
            $data['tenant_id'] = $tenant->id;
            $data['landlord_id'] = $contract->landlord_id;
            $data['payment_status'] = 'pending';
            $data['paid_at'] = date('Y-m-d H:i:s');
            
            // Handle file upload - remove from data first to avoid array access error
            $uploadedFile = $this->request->getUploadedFile('proof');
            unset($data['proof']); // Remove the file object from data array
            
            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                $uploadPath = $this->uploadProof($uploadedFile);
                if ($uploadPath) {
                    $data['proof_path'] = $uploadPath; // Add the file path string instead
                }
            }
            
            // Online payment is disabled
            if ($data['payment_method'] === 'online') {
                $this->Flash->error('Online payment is coming soon');
                return $this->redirect(['action' => 'add']);
            }
            
            $payment = $this->Payments->patchEntity($payment, $data);
            
            if ($this->Payments->save($payment)) {
                // Generate payment receipt/slip immediately
                try {
                    $pdfService = new PdfService();
                    $receiptPath = $pdfService->generateReceipt($payment->id);
                    
                    // Update payment with receipt path
                    $payment->receipt_path = $receiptPath;
                    $this->Payments->save($payment);
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('Failed to generate receipt: ' . $e->getMessage());
                    // Don't fail the payment if receipt generation fails
                }
                
                $this->Flash->success('Payment submitted. Waiting for verification. You can download your payment slip from the payment details.');
                return $this->redirect(['action' => 'view', $payment->id]);
            }
            
            $this->Flash->error('Failed to submit payment');
        }
        
        $this->set(compact('payment', 'contracts'));
    }

    public function verify($id = null)
    {
        $this->request->allowMethod(['post']);
        
        $user = $this->Authentication->getIdentity();
        
        if ($user->role !== 'landlord' && $user->role !== 'admin') {
            $this->Flash->error('Access denied');
            return $this->redirect(['action' => 'index']);
        }
        
        $payment = $this->Payments->get($id, [
            'contain' => ['Tenants', 'Landlords', 'Contracts'],
        ]);
        
        $action = $this->request->getData('action'); // 'verify' or 'reject'
        
        if ($action === 'verify') {
            $payment->payment_status = 'verified';
            $payment->verified_at = date('Y-m-d H:i:s');
            $payment->verified_by = $user->id;
            
            if ($this->Payments->save($payment)) {
                // Generate invoice
                $pdfService = new PdfService();
                $invoicePath = $pdfService->generateInvoice($payment->id);
                
                // Save invoice record
                $this->loadModel('Invoices');
                $invoice = $this->Invoices->newEmptyEntity([
                    'payment_id' => $payment->id,
                    'invoice_number' => 'INV-' . str_pad((string)$payment->id, 6, '0', STR_PAD_LEFT),
                    'file_path' => $invoicePath,
                ]);
                $this->Invoices->save($invoice);
                
                // Send notifications via Firebase
                $firebase = new FirebaseService();
                $tenantUser = $payment->tenant->user;
                
                if ($tenantUser->phone) {
                    $firebase->sendPaymentVerificationSms($tenantUser->phone, [
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'reference' => $payment->reference ?? $payment->id,
                    ]);
                }
                
                if ($tenantUser->email) {
                    $firebase->sendEmail(
                        $tenantUser->email,
                        'Payment Verified',
                        "Your payment of {$payment->currency} {$payment->amount} has been verified.",
                        ['payment_id' => $payment->id]
                    );
                }
                
                $this->Flash->success('Payment verified successfully');
            } else {
                $this->Flash->error('Failed to verify payment');
            }
        } elseif ($action === 'reject') {
            $payment->payment_status = 'rejected';
            $payment->remarks = $this->request->getData('remarks');
            
            if ($this->Payments->save($payment)) {
                // Send rejection notification
                $firebase = new FirebaseService();
                $tenantUser = $payment->tenant->user;
                
                if ($tenantUser->phone) {
                    $firebase->sendPaymentRejectionSms($tenantUser->phone, [
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'reason' => $payment->remarks ?? 'Payment rejected',
                    ]);
                }
                
                if ($tenantUser->email) {
                    $firebase->sendEmail(
                        $tenantUser->email,
                        'Payment Rejected',
                        "Your payment of {$payment->currency} {$payment->amount} has been rejected. Reason: " . ($payment->remarks ?? 'N/A'),
                        ['payment_id' => $payment->id]
                    );
                }
                
                $this->Flash->success('Payment rejected');
            } else {
                $this->Flash->error('Failed to reject payment');
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }

    public function downloadInvoice($id = null)
    {
        $payment = $this->Payments->get($id, [
            'contain' => ['Invoices'],
        ]);
        
        if (!$payment->invoice) {
            // Generate invoice if not exists
            $pdfService = new PdfService();
            $invoicePath = $pdfService->generateInvoice($payment->id);
            
            $this->loadModel('Invoices');
            $invoice = $this->Invoices->newEmptyEntity([
                'payment_id' => $payment->id,
                'invoice_number' => 'INV-' . str_pad((string)$payment->id, 6, '0', STR_PAD_LEFT),
                'file_path' => $invoicePath,
            ]);
            $this->Invoices->save($invoice);
            
            $filePath = WWW_ROOT . $invoicePath;
        } else {
            $filePath = WWW_ROOT . $payment->invoice->file_path;
        }
        
        if (file_exists($filePath)) {
            return $this->response->withFile($filePath, [
                'download' => true,
                'name' => 'invoice_' . $payment->id . '.pdf',
            ]);
        }
        
        $this->Flash->error('Invoice not found');
        return $this->redirect(['action' => 'index']);
    }

    public function downloadReceipt($id = null)
    {
        $user = $this->Authentication->getIdentity();
        
        try {
            $payment = $this->Payments->get($id, [
                'contain' => ['Tenants', 'Landlords'],
            ]);
            
            // Check access permissions
            if ($user->role === 'tenant') {
                $this->loadModel('Tenants');
                $tenant = $this->Tenants->find()
                    ->where(['user_id' => $user->id])
                    ->first();
                
                if (!$tenant || $payment->tenant_id !== $tenant->id) {
                    $this->Flash->error('Access denied');
                    return $this->redirect(['action' => 'index']);
                }
            } elseif ($user->role === 'landlord') {
                $this->loadModel('Landlords');
                $landlord = $this->Landlords->find()
                    ->where(['user_id' => $user->id])
                    ->first();
                
                if (!$landlord || $payment->landlord_id !== $landlord->id) {
                    $this->Flash->error('Access denied');
                    return $this->redirect(['action' => 'index']);
                }
            }
            
            // Generate receipt if it doesn't exist
            if (empty($payment->receipt_path)) {
                try {
                    $pdfService = new PdfService();
                    $receiptPath = $pdfService->generateReceipt($payment->id);
                    
                    $payment->receipt_path = $receiptPath;
                    $this->Payments->save($payment);
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('Failed to generate receipt: ' . $e->getMessage());
                    $this->Flash->error('Failed to generate receipt. Please try again.');
                    return $this->redirect(['action' => 'view', $id]);
                }
            }
            
            $filePath = WWW_ROOT . $payment->receipt_path;
            
            if (file_exists($filePath)) {
                return $this->response->withFile($filePath, [
                    'download' => true,
                    'name' => 'payment_receipt_' . $payment->id . '.pdf',
                ]);
            }
            
            // If file doesn't exist, regenerate it
            try {
                $pdfService = new PdfService();
                $receiptPath = $pdfService->generateReceipt($payment->id);
                $payment->receipt_path = $receiptPath;
                $this->Payments->save($payment);
                
                $filePath = WWW_ROOT . $receiptPath;
                if (file_exists($filePath)) {
                    return $this->response->withFile($filePath, [
                        'download' => true,
                        'name' => 'payment_receipt_' . $payment->id . '.pdf',
                    ]);
                }
            } catch (\Exception $e) {
                \Cake\Log\Log::error('Failed to regenerate receipt: ' . $e->getMessage());
                $this->Flash->error('Failed to generate receipt: ' . $e->getMessage());
                return $this->redirect(['action' => 'view', $id]);
            }
            
            $this->Flash->error('Receipt not found');
            return $this->redirect(['action' => 'view', $id]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Payment not found');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            \Cake\Log\Log::error('Error downloading receipt: ' . $e->getMessage());
            \Cake\Log\Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->Flash->error('An error occurred while downloading the receipt. Please try again.');
            return $this->redirect(['action' => 'view', $id ?? null]);
        }
    }

    private function uploadProof($uploadedFile): ?string
    {
        // Check if it's a PSR-7 UploadedFileInterface object
        if (!($uploadedFile instanceof \Psr\Http\Message\UploadedFileInterface)) {
            return null;
        }
        
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $clientFilename = $uploadedFile->getClientFilename();
        $clientMediaType = $uploadedFile->getClientMediaType();
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($clientMediaType, $allowedTypes)) {
            return null;
        }
        
        $extension = pathinfo($clientFilename, PATHINFO_EXTENSION);
        $filename = 'proof_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadDir = WWW_ROOT . 'uploads' . DS . 'proofs' . DS;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filePath = $uploadDir . $filename;
        
        try {
            // Move the uploaded file
            $uploadedFile->moveTo($filePath);
            return 'uploads/proofs/' . $filename;
        } catch (\Exception $e) {
            \Cake\Log\Log::error('File upload error: ' . $e->getMessage());
            return null;
        }
    }
}

