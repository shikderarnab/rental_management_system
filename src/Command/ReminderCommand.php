<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;
use App\Service\FirebaseService;

class ReminderCommand extends Command
{
    protected $modelClass = 'Reminders';

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('Starting reminder processing...');
        
        $remindersTable = TableRegistry::getTableLocator()->get('Reminders');
        $contractsTable = TableRegistry::getTableLocator()->get('Contracts');
        $firebase = new FirebaseService();
        
        // Get reminders scheduled for today or overdue
        $reminders = $remindersTable->find()
            ->where([
                'OR' => [
                    'scheduled_at <=' => date('Y-m-d H:i:s'),
                    'scheduled_at IS' => null,
                ],
                'sent_at IS' => null,
            ])
            ->contain(['Contracts', 'Tenants'])
            ->toArray();
        
        foreach ($reminders as $reminder) {
            $tenant = $reminder->tenant;
            $user = $tenant->user;
            $contract = $reminder->contract;
            
            $sent = false;
            
            // Send SMS if enabled
            if ($reminder->send_sms && !$reminder->sms_sent && $user->phone) {
                $reminderData = [
                    'amount' => $contract->rent_amount,
                    'currency' => $contract->currency,
                    'due_date' => $contract->start_date->format('Y-m-d'),
                ];
                
                $result = $firebase->sendRentDueReminderSms($user->phone, $reminderData);
                
                if ($result['success']) {
                    $reminder->sms_sent = true;
                    $sent = true;
                }
            }
            
            // Send Email if enabled
            if ($reminder->send_email && !$reminder->email_sent && $user->email) {
                $subject = 'Rent Due Reminder';
                $body = "Your rent payment of {$contract->currency} {$contract->rent_amount} is due.";
                
                $result = $firebase->sendEmail($user->email, $subject, $body);
                
                if ($result['success']) {
                    $reminder->email_sent = true;
                    $sent = true;
                }
            }
            
            if ($sent) {
                $reminder->sent_at = date('Y-m-d H:i:s');
                $remindersTable->save($reminder);
                
                $io->out("Reminder sent for contract #{$contract->id}");
            }
        }
        
        // Create new reminders for upcoming rent due dates
        $this->createRentDueReminders($io);
        
        $io->out('Reminder processing completed.');
    }

    private function createRentDueReminders(ConsoleIo $io)
    {
        $contractsTable = TableRegistry::getTableLocator()->get('Contracts');
        $remindersTable = TableRegistry::getTableLocator()->get('Reminders');
        
        // Get active contracts
        $contracts = $contractsTable->find()
            ->where(['status' => 'active'])
            ->contain(['Tenants'])
            ->toArray();
        
        foreach ($contracts as $contract) {
            // Check if reminder already exists for this month
            $existing = $remindersTable->find()
                ->where([
                    'contract_id' => $contract->id,
                    'reminder_type' => 'rent_due',
                    'scheduled_at >=' => date('Y-m-01'),
                ])
                ->first();
            
            if (!$existing) {
                // Create reminder for next rent due date
                $reminder = $remindersTable->newEmptyEntity([
                    'contract_id' => $contract->id,
                    'tenant_id' => $contract->tenant_id,
                    'reminder_type' => 'rent_due',
                    'message' => "Rent payment of {$contract->currency} {$contract->rent_amount} is due.",
                    'send_sms' => true,
                    'send_email' => true,
                    'scheduled_at' => date('Y-m-d', strtotime('+7 days')),
                ]);
                
                $remindersTable->save($reminder);
                $io->out("Created reminder for contract #{$contract->id}");
            }
        }
    }
}

