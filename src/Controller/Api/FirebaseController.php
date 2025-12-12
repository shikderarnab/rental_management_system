<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\FirebaseService;

class FirebaseController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['sendOtp', 'verifyOtp']);
    }

    /**
     * Send OTP via Firebase Phone Auth
     */
    public function sendOtp()
    {
        $this->request->allowMethod(['post']);
        
        $phoneNumber = $this->request->getData('phone_number');
        
        if (empty($phoneNumber)) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'error' => 'Phone number is required',
                ]));
        }
        
        $firebase = new FirebaseService();
        $result = $firebase->sendOtpSms($phoneNumber);
        
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp()
    {
        $this->request->allowMethod(['post']);
        
        $sessionInfo = $this->request->getData('session_info');
        $code = $this->request->getData('code');
        
        if (empty($sessionInfo) || empty($code)) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'error' => 'Session info and code are required',
                ]));
        }
        
        $firebase = new FirebaseService();
        $result = $firebase->verifyOtp($sessionInfo, $code);
        
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }
}

