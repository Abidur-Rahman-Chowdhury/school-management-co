<?php

namespace App\Controllers\Auth;

use App\Constant\Constants;
use App\Controllers\BaseController;
use App\Models\DynamicModel;
use App\Models\CnfModel;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class Auth extends BaseController
{
    protected $parentId = null;
    protected $currentDistrictId = null;
    protected $data;
    protected $db;
    protected $dModel;
    protected $session;
    protected $thumbnail;
    public function __construct()
    {
        helper('form');
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->dModel = new DynamicModel();
        $this->thumbnail = true;
    }

    public function login()
    {
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (!empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/' . Constants::USER_GROUP_ROUTE[$userGroup]);
        }
        if ($this->request->getPost()) {
            $loginData = $this->request->getPost();
            $rules = [
                "email"    => 'required|valid_email|is_not_unique[users.email]',
                "password" => 'required',
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());
                return redirect()->to(rtrim(BASE_URL, '/') . '/login');
            }
            $email = $this->dModel->validateEmailOrPhone($loginData['email']);
            $password = $loginData['password'];
            $result = $this->dModel->dynamicCheckExistSingleRow(['email' => $email['content']], Constants::TABLE_USERS);
            if (empty($result)) {
                return redirect()->to($this->dModel->falseReturn('/login', 'You are trying to enter someone else admin panel'));
            }
            if ($result['is_active'] == Constants::DEACTIVE) {
                return redirect()->to($this->dModel->falseReturn('/login', 'Your account has been blocked contact with the admin'));
            }
            if ($result) {
                if (!password_verify($password, $result['password'])) {
                    $this->dModel->dynamicUpdate(['id' => $result['id']], Constants::TABLE_USERS, ['login_failed' =>  $result['login_failed'] + 1]);
                    if ($result['login_failed'] > Constants::BLOCK_COUNT) {
                        $this->dModel->dynamicUpdate(['id' => $result['id']], Constants::TABLE_USERS, ['is_active' =>  Constants::DEACTIVE]);
                        return redirect()->to($this->dModel->falseReturn('/login', 'Your account has been blocked due to multiple failed login attempts.'));
                    }
                    return redirect()->to($this->dModel->falseReturn('/login', 'Invalid  email or password!!!'));
                }
                $this->dModel->dynamicUpdate(['id' => $result['id']], Constants::TABLE_USERS, ['login_failed' =>  Constants::DEACTIVE]);
                if($result['auth_req'] == Constants::AUTH_REQ_NO) {
                    $userData[$result['user_group']] = [
                        'client_id'          => $result['client_id'],
                        'op_id'              => $result['id'],
                        'email'              => $result['email'],
                        'name'          => $result['name'],
                        'last_name'          => $result['last_name'],
                        'user_group'          => $result['user_group'],
                       

                    ];
                    $userData['user_group'] = $result['user_group'];
                    session()->set($userData);
                    return redirect()->to($this->dModel->successReturn('/'. Constants::USER_GROUP_ROUTE[$result['user_group']], ''));
                } elseif($result['auth_req'] == Constants::AUTH_REQ_YES) {
                    if(is_null($result['secret_key']) || strlen($result['secret_key']) < 16) {
                        $userData['login'] = [
                            'client_id'          => $result['client_id'],
                            'op_id'              => $result['id'],
                        ];
                        session()->set($userData);
                        return redirect()->to($this->dModel->falseReturn('/login/setup-secret-key', ''));
                    } else {
                        $userData['login'] = [
                            'client_id'          => $result['client_id'],
                            'op_id'              => $result['id'],
                        ];
                        session()->set($userData);
                        return redirect()->to($this->dModel->successReturn('/login/google-authenticator', ''));
                    }
                }
              
            }
        }
        $this->data['formUrl'] = BASE_URL . 'login';
        return view('auth/login', $this->data);
    }
    public function googleAuthenticator()
    {
        $credential = session()->get('login');
        $userGroup = session()->get('user_group');

        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();

        if ($this->request->getPost()) {
            $otp = $this->request->getPost('otp');
            $user = $this->dModel->dynamicCheckExistSingleRow(['client_id' => $credential['client_id'], 'id' => $credential['op_id']], Constants::TABLE_USERS);
            $_g2fa = new Google2FA();
        //     $url = $_g2fa->getQRCodeUrl('test', 'kayes', $user['secret_key']);
        //    $d = 'http://chart.apis.google.com/chart?chs=' . 100 . 'x' . 100 .
        //     '&chld=M|0&cht=qr&chl=' . urlencode($url);
            
        //     dd($d);
            $verify = $_g2fa->verify($otp, $user['secret_key']);
            if ($verify) {
                $otpData[$user['user_group']] = [
                    'client_id'          => $user['client_id'],
                    'op_id'              => $user['id'],
                    'email'              => $user['email'],
                    'name'          => $user['name'],
                    'last_name'          => $user['last_name'],
                    'user_group'          => $user['user_group'],
                    'secret_key' => $user['secret_key'],
                    'validation' => true
                ];
                $otpData['user_group'] = $user['user_group'];
                session()->set($otpData);
                return redirect()->to($this->dModel->successReturn('/' . Constants::USER_GROUP_ROUTE[$user['user_group']], ''));
            }
            return redirect()->to($this->dModel->falseReturn('/login/google-authenticator', 'Invalid OTP'));

            //    380329
        }
        $this->data['formUrl'] = BASE_URL . 'login/google-authenticator';
        return view('layout/super-admin/google-authenticator', $this->data);
    }
    public function setUpSecretKey()
    {
        $credential = session()->get('login');
        $userGroup = session()->get('user_group');
        $_g2fa = new Google2FA();
       
       
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $user = $this->dModel->dynamicCheckExistSingleRow(['client_id' => $credential['client_id'], 'id' => $credential['op_id']], Constants::TABLE_USERS);
        $secretKey = $_g2fa->generateSecretKey();
        $this->data['secretKey'] = $secretKey;
        // $app_name = 'school management';
        // $email =  $user['email'];
        // $qrCodeUrl =  $_g2fa->getQRCodeUrl(
        //     $app_name,
        //     $email,
        //     $secretKey
        // );
        // $renderer = new ImageRenderer(
        //     new RendererStyle(250),
        //     new ImagickImageBackEnd()
        // );
        // $writer = new Writer($renderer);
        // $writer->writeFile($qrCodeUrl, 'qr.png');
        // $encoded_qr_data = base64_encode($writer->writeString($qrCodeUrl));
        // $this->data['imgSrc'] =  $encoded_qr_data;
        if ($this->request->getPost()) {
            $pData = $this->request->getPost();
            $verify = $_g2fa->verify($pData['otp'],  $pData['secret_key']);
            if ($verify) {
                $this->dModel->dynamicUpdate(['id' => $user['id']], Constants::TABLE_USERS, ['secret_key' => $secretKey]);
                $otpData[$user['user_group']] = [
                    'client_id'          => $user['client_id'],
                    'op_id'              => $user['id'],
                    'email'              => $user['email'],
                    'name'          => $user['name'],
                    'last_name'          => $user['last_name'],
                    'user_group'          => $user['user_group'],
                ];
                $otpData['user_group'] = $user['user_group'];
                session()->set($otpData);
                return redirect()->to($this->dModel->successReturn('/' . Constants::USER_GROUP_ROUTE[$user['user_group']], ''));
            }
            return redirect()->to($this->dModel->falseReturn('/login/setup-secret-key', 'Invalid OTP'));

        
        }
        $this->data['formUrl'] = BASE_URL . 'login/setup-secret-key';
        return view('layout/super-admin/setup-key', $this->data);
    }
   
    /* logged out */
    public function logout()
    {

        session()->destroy();
        return redirect()->to(base_url('/login'));
    }

    /* view office leave data */
}
