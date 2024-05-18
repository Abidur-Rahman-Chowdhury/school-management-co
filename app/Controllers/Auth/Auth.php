<?php

namespace App\Controllers\Auth;

use App\Constant\Constants;
use App\Controllers\BaseController;
use App\Models\DynamicModel;
use App\Models\CnfModel;

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
                return redirect()->to($this->dModel->successReturn('/' . Constants::USER_GROUP_ROUTE[$result['user_group']], ''));
            }
        }
        $this->data['formUrl'] = BASE_URL . 'login';
        return view('auth/login', $this->data);
    }
    /* logged out */
    public function logout()
    {

        session()->destroy();
        return redirect()->to(base_url('/login'));
    }

    /* view office leave data */
}
