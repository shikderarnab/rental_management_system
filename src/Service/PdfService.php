<?php
declare(strict_types=1);

namespace App\Service;

use TCPDF;
use Cake\ORM\TableRegistry;

class PdfService
{
    /**
     * Generate invoice PDF
     * 
     * @param int $paymentId Payment ID
     * @return string PDF file path
     */
    public function generateInvoice(int $paymentId): string
    {
        $paymentsTable = TableRegistry::getTableLocator()->get('Payments');
        $payment = $paymentsTable->get($paymentId);
        
        // Load relationships separately to avoid SQL ambiguity
        $paymentsTable->loadInto($payment, [
            'Contracts',
            'Tenants' => ['Users'],
            'Landlords' => ['Users']
        ]);
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator('Rental Management System');
        $pdf->SetAuthor('Rental Management System');
        $pdf->SetTitle('Invoice #' . $paymentId);
        $pdf->SetSubject('Payment Invoice');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->AddPage();
        
        // Invoice content
        $html = $this->getInvoiceHtml($payment);
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $filename = 'invoice_' . $paymentId . '_' . date('YmdHis') . '.pdf';
        $filepath = WWW_ROOT . 'uploads' . DS . 'invoices' . DS . $filename;
        
        // Create directory if it doesn't exist
        $dir = dirname($filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $pdf->Output($filepath, 'F');
        
        return 'uploads/invoices/' . $filename;
    }

    /**
     * Generate receipt PDF
     * 
     * @param int $paymentId Payment ID
     * @return string PDF file path
     */
    public function generateReceipt(int $paymentId): string
    {
        try {
            $paymentsTable = TableRegistry::getTableLocator()->get('Payments');
            
            // Load payment with relationships separately to avoid SQL ambiguity
            $payment = $paymentsTable->get($paymentId);
            
            // Load relationships separately
            $paymentsTable->loadInto($payment, [
                'Contracts',
                'Tenants' => ['Users'],
                'Landlords' => ['Users']
            ]);
            
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            $pdf->SetCreator('Rental Management System');
            $pdf->SetAuthor('Rental Management System');
            $pdf->SetTitle('Receipt #' . $paymentId);
            $pdf->SetSubject('Payment Receipt');
            
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            $pdf->AddPage();
            
            // Receipt content
            $html = $this->getReceiptHtml($payment);
            
            $pdf->writeHTML($html, true, false, true, false, '');
            
            $filename = 'receipt_' . $paymentId . '_' . date('YmdHis') . '.pdf';
            $filepath = WWW_ROOT . 'uploads' . DS . 'receipts' . DS . $filename;
            
            // Create directory if it doesn't exist
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $pdf->Output($filepath, 'F');
            
            return 'uploads/receipts/' . $filename;
        } catch (\Exception $e) {
            \Cake\Log\Log::error('Error in generateReceipt: ' . $e->getMessage());
            \Cake\Log\Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get invoice HTML content
     */
    private function getInvoiceHtml($payment): string
    {
        $invoiceNumber = 'INV-' . str_pad((string)$payment->id, 6, '0', STR_PAD_LEFT);
        
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 30px; }
            .invoice-info { margin-bottom: 20px; }
            .invoice-details { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; }
            .row { margin-bottom: 10px; }
            .label { font-weight: bold; display: inline-block; width: 150px; }
            .footer { margin-top: 30px; text-align: center; color: #666; }
        </style>
        
        <div class="header">
            <h1>INVOICE</h1>
            <p>Rental Management System</p>
        </div>
        
        <div class="invoice-info">
            <div class="row">
                <span class="label">Invoice Number:</span>
                <span>' . h($invoiceNumber) . '</span>
            </div>
            <div class="row">
                <span class="label">Date:</span>
                <span>' . h($this->formatDate($payment->created ?? null)) . '</span>
            </div>
        </div>
        
        <div class="invoice-details">
            <h3>Payment Details</h3>
            <div class="row">
                <span class="label">Tenant:</span>
                <span>' . h($payment->tenant->user->first_name . ' ' . $payment->tenant->user->last_name) . '</span>
            </div>
            <div class="row">
                <span class="label">Landlord:</span>
                <span>' . h($payment->landlord->user->first_name . ' ' . $payment->landlord->user->last_name) . '</span>
            </div>
            <div class="row">
                <span class="label">Amount:</span>
                <span>' . h(($payment->currency ?? 'USD') . ' ' . number_format((float)($payment->amount ?? 0), 2)) . '</span>
            </div>
            <div class="row">
                <span class="label">Payment Method:</span>
                <span>' . h(ucfirst($payment->payment_method)) . '</span>
            </div>
            <div class="row">
                <span class="label">Status:</span>
                <span>' . h(ucfirst($payment->payment_status)) . '</span>
            </div>
            ' . (!empty($payment->reference) ? '<div class="row"><span class="label">Reference:</span><span>' . h($payment->reference) . '</span></div>' : '') . '
        </div>
        
        <div class="footer">
            <p>This is a computer-generated invoice. No signature required.</p>
        </div>
        ';
        
        return $html;
    }

    /**
     * Get receipt HTML content
     */
    private function getReceiptHtml($payment): string
    {
        $receiptNumber = 'RCP-' . str_pad((string)$payment->id, 6, '0', STR_PAD_LEFT);
        
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 30px; }
            .receipt-info { margin-bottom: 20px; }
            .receipt-details { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; }
            .row { margin-bottom: 10px; }
            .label { font-weight: bold; display: inline-block; width: 150px; }
            .footer { margin-top: 30px; text-align: center; color: #666; }
        </style>
        
        <div class="header">
            <h1>PAYMENT RECEIPT</h1>
            <p>Rental Management System</p>
        </div>
        
        <div class="receipt-info">
            <div class="row">
                <span class="label">Receipt Number:</span>
                <span>' . h($receiptNumber) . '</span>
            </div>
            <div class="row">
                <span class="label">Date:</span>
                <span>' . h($this->formatDate($payment->paid_at ?? $payment->created ?? null)) . '</span>
            </div>
        </div>
        
        <div class="receipt-details">
            <h3>Payment Received</h3>
            <div class="row">
                <span class="label">From:</span>
                <span>' . h($this->getTenantName($payment)) . '</span>
            </div>
            <div class="row">
                <span class="label">To:</span>
                <span>' . h($this->getLandlordName($payment)) . '</span>
            </div>
            <div class="row">
                <span class="label">Amount:</span>
                <span><strong>' . h(($payment->currency ?? 'USD') . ' ' . number_format((float)($payment->amount ?? 0), 2)) . '</strong></span>
            </div>
            <div class="row">
                <span class="label">Payment Method:</span>
                <span>' . h(ucfirst($payment->payment_method ?? 'N/A')) . '</span>
            </div>
            ' . (!empty($payment->reference) ? '<div class="row"><span class="label">Reference:</span><span>' . h($payment->reference) . '</span></div>' : '') . '
        </div>
        
        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This is a computer-generated receipt. No signature required.</p>
        </div>
        ';
        
        return $html;
    }

    /**
     * Safely format a date value
     * 
     * @param mixed $date Date value (DateTime, string, or null)
     * @return string Formatted date string
     */
    private function formatDate($date): string
    {
        if (empty($date)) {
            return date('Y-m-d H:i:s');
        }
        
        if ($date instanceof \DateTime || $date instanceof \Cake\I18n\FrozenTime || $date instanceof \Cake\I18n\Time) {
            return $date->format('Y-m-d H:i:s');
        }
        
        if (is_string($date)) {
            try {
                $dateTime = new \DateTime($date);
                return $dateTime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return date('Y-m-d H:i:s');
            }
        }
        
        return date('Y-m-d H:i:s');
    }

    /**
     * Get tenant name safely
     * 
     * @param object $payment Payment entity
     * @return string Tenant name
     */
    private function getTenantName($payment): string
    {
        if (!isset($payment->tenant)) {
            return 'Tenant #' . ($payment->tenant_id ?? 'N/A');
        }
        
        if (isset($payment->tenant->user)) {
            $firstName = $payment->tenant->user->first_name ?? '';
            $lastName = $payment->tenant->user->last_name ?? '';
            $name = trim($firstName . ' ' . $lastName);
            if (!empty($name)) {
                return $name;
            }
        }
        
        return 'Tenant #' . ($payment->tenant_id ?? 'N/A');
    }

    /**
     * Get landlord name safely
     * 
     * @param object $payment Payment entity
     * @return string Landlord name
     */
    private function getLandlordName($payment): string
    {
        if (!isset($payment->landlord)) {
            return 'Landlord #' . ($payment->landlord_id ?? 'N/A');
        }
        
        // Check for company name first
        if (!empty($payment->landlord->company_name)) {
            return $payment->landlord->company_name;
        }
        
        // Then check for user name
        if (isset($payment->landlord->user)) {
            $firstName = $payment->landlord->user->first_name ?? '';
            $lastName = $payment->landlord->user->last_name ?? '';
            $name = trim($firstName . ' ' . $lastName);
            if (!empty($name)) {
                return $name;
            }
        }
        
        return 'Landlord #' . ($payment->landlord_id ?? 'N/A');
    }
}

