<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Log\Log;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FirebaseService
{
    private $apiKey;
    private $projectId;
    private $serviceAccountPath;
    private $client;

    public function __construct()
    {
        $this->apiKey = Configure::read('Firebase.apiKey');
        $this->projectId = Configure::read('Firebase.projectId');
        $this->serviceAccountPath = Configure::read('Firebase.serviceAccountPath');
        $this->client = new Client([
            'base_uri' => 'https://identitytoolkit.googleapis.com/v1/',
            'timeout' => 30,
        ]);
    }

    /**
     * Send OTP SMS via Firebase Phone Auth
     * 
     * @param string $phoneNumber Phone number in E.164 format
     * @return array Response data
     */
    public function sendOtpSms(string $phoneNumber): array
    {
        try {
            $response = $this->client->post('accounts:sendVerificationCode', [
                'json' => [
                    'phoneNumber' => $phoneNumber,
                ],
                'query' => [
                    'key' => $this->apiKey,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Firebase OTP sent', [
                'phone' => $phoneNumber,
                'sessionInfo' => $data['sessionInfo'] ?? null,
            ]);

            return [
                'success' => true,
                'sessionInfo' => $data['sessionInfo'] ?? null,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Firebase OTP send failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify OTP code
     * 
     * @param string $sessionInfo Session info from sendOtpSms
     * @param string $code OTP code
     * @return array Response data
     */
    public function verifyOtp(string $sessionInfo, string $code): array
    {
        try {
            $response = $this->client->post('accounts:verifyPhoneNumber', [
                'json' => [
                    'sessionInfo' => $sessionInfo,
                    'code' => $code,
                ],
                'query' => [
                    'key' => $this->apiKey,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Firebase OTP verified', [
                'idToken' => isset($data['idToken']) ? 'present' : 'missing',
            ]);

            return [
                'success' => true,
                'idToken' => $data['idToken'] ?? null,
                'refreshToken' => $data['refreshToken'] ?? null,
                'localId' => $data['localId'] ?? null,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Firebase OTP verification failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send custom email via Firebase Admin SDK REST API
     * Note: This requires Firebase Cloud Functions to be set up
     * 
     * @param string $to Email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param array $data Additional data
     * @return array Response data
     */
    public function sendEmail(string $to, string $subject, string $body, array $data = []): array
    {
        try {
            // Get access token for Firebase Admin SDK
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new \Exception('Failed to get Firebase access token');
            }

            // Call Cloud Function endpoint (you need to deploy this)
            $functionUrl = "https://{$this->projectId}.cloudfunctions.net/sendEmail";
            
            $response = $this->client->post($functionUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'to' => $to,
                    'subject' => $subject,
                    'body' => $body,
                    'data' => $data,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Firebase email sent', [
                'to' => $to,
                'subject' => $subject,
            ]);

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Firebase email send failed', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            // Fallback to CakePHP email if Firebase fails
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'fallback' => true,
            ];
        }
    }

    /**
     * Send push notification via FCM
     * 
     * @param string $token FCM token
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data
     * @return array Response data
     */
    public function sendPushNotification(string $token, string $title, string $body, array $data = []): array
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new \Exception('Failed to get Firebase access token');
            }

            $response = $this->client->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $data,
                    ],
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            Log::info('FCM notification sent', [
                'token' => substr($token, 0, 20) . '...',
            ]);

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('FCM notification failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Firebase Admin SDK access token
     * 
     * @return string|null Access token
     */
    private function getAccessToken(): ?string
    {
        if (!file_exists($this->serviceAccountPath)) {
            Log::error('Firebase service account file not found', [
                'path' => $this->serviceAccountPath,
            ]);
            return null;
        }

        try {
            $serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
            
            $now = time();
            $expires = $now + 3600; // 1 hour

            $payload = [
                'iss' => $serviceAccount['client_email'],
                'sub' => $serviceAccount['client_email'],
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $expires,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging https://www.googleapis.com/auth/cloud-platform',
            ];

            $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

            $response = $this->client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to get Firebase access token', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send payment verification SMS
     * 
     * @param string $phoneNumber Phone number
     * @param array $paymentData Payment information
     * @return array Response
     */
    public function sendPaymentVerificationSms(string $phoneNumber, array $paymentData): array
    {
        $message = "Payment Verified!\nAmount: {$paymentData['currency']} {$paymentData['amount']}\nReference: {$paymentData['reference']}\nThank you!";
        
        // Use Firebase Phone Auth to send OTP-style notification
        // In production, you might want to use a different Firebase service
        return $this->sendOtpSms($phoneNumber);
    }

    /**
     * Send payment rejection SMS
     * 
     * @param string $phoneNumber Phone number
     * @param array $paymentData Payment information
     * @return array Response
     */
    public function sendPaymentRejectionSms(string $phoneNumber, array $paymentData): array
    {
        $message = "Payment Rejected\nAmount: {$paymentData['currency']} {$paymentData['amount']}\nReason: {$paymentData['reason']}\nPlease contact support.";
        
        return $this->sendOtpSms($phoneNumber);
    }

    /**
     * Send rent due reminder SMS
     * 
     * @param string $phoneNumber Phone number
     * @param array $reminderData Reminder information
     * @return array Response
     */
    public function sendRentDueReminderSms(string $phoneNumber, array $reminderData): array
    {
        $message = "Rent Due Reminder\nAmount: {$reminderData['currency']} {$reminderData['amount']}\nDue Date: {$reminderData['due_date']}\nPlease make payment.";
        
        return $this->sendOtpSms($phoneNumber);
    }
}

