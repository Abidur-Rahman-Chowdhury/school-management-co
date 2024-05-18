<?php

namespace App\Controllers\SuperAdmin;

use App\Constant\Constants;
use App\Controllers\BaseController;
use App\Models\SchoolModel;
use App\Models\DynamicModel;



class SuperAdminSetting extends BaseController
{
    protected $parentId = null;
    protected $currentDistrictId = null;
    protected $data;
    protected $db;
    protected $dModel;
    protected $schoolModel;
    protected $session;
    protected $thumbnail;
    public function __construct()
    {
        helper('form');
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->dModel = new DynamicModel();
        $this->schoolModel = new SchoolModel();
        $this->thumbnail = true;
    }

    public function addStateData()
    {

        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        $this->data['states'] = $this->schoolModel->selectStatesByParentId(Constants::TABLE_STATE, 'name', 'ASC', 0);
        if ($this->request->getPost()) {
            $statesData = $this->request->getPost();
            $rules = [
                "name" => 'required|is_unique[state.name]',
                "sort" => 'required',
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());

                return redirect()->to(base_url('super-admin/add-state'));
            }
            $cData = [
                'parent_id' => $statesData['parent_id'] ?? null,
                'name' => $statesData['name'],
                'reference' => $statesData['reference'],
                'sort' => $statesData['sort'],
            ];
            $cId = $this->dModel->dynamicInsertReturnId($cData, Constants::TABLE_STATE);
            if (!$cId) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-state', 'Could not add state data'));
            }
            return redirect()->to($this->dModel->successReturn('/super-admin/add-state', 'State data added successfully!!!'));
        }
        $this->data['formUrl'] = BASE_URL . 'super-admin/add-state';
        $this->data['states'] = $this->statesTree();
        return view('layout/super-admin/add-state-data', $this->data);
    }
    public function createStatesTree($currentId = null, $parent_id = 0, $sub_mark = '', &$options = '')
    {
        $result = $this->schoolModel->selectStatesByParentId(Constants::TABLE_STATE, 'name', 'ASC', $parent_id);
        $arraylen = count($result);
        if ($arraylen > 0) {
            for ($i = 0; $i < $arraylen; $i++) {
                $row = $result[$i];
                $selected = '';
                if ($this->parentId == $row['id']) {
                    $selected = 'selected';
                }
                $options .= '<option ' . $selected . ' value="' . $row['id'] . '">' . $sub_mark . $row['name'] . '</option>';
                $this->createStatesTree($currentId, $row['id'], $sub_mark . '---', $options);
            }
        }
        return $options;
    }
    public function statesTree($id = null)
    {
        return $this->createStatesTree($id);
    }
    public function updateStatesData($id = null)
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $this->data['statesData'] = $this->dModel->dynamicCheckExist(['id' => $id], Constants::TABLE_STATE);
        $this->parentId = $this->data['statesData'][0]['parent_id'];
        $this->data['states'] = $this->statesTree($id);
        $statesData = $this->dModel->dynamicCheckExist(['id' => $id], Constants::TABLE_STATE);
        if (empty($statesData)) {
            return redirect()->to($this->dModel->falseReturn('/super-admin/show-states', 'States data not found!!!'));
        }
        if ($this->request->getPost()) {
            $sData = $this->request->getPost();
            $rules = [
                "name" => 'required',
                "sort" => 'required',
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());
                return redirect()->to(base_url('super-admin/edit-states-data/' . $id));
            }
            if ($statesData[0]['parent_id'] !== $sData['parent_id']) {
                $updateStates = [
                    'parent_id' => $sData['parent_id'],
                    'name' => $sData['name'],
                    'reference' => $sData['reference'],
                    'sort' => $sData['sort'],
                ];
            } else {
                $updateStates = [
                    'name' => $sData['name'],
                    'reference' => $sData['reference'],
                    'sort' => $sData['sort'],
                ];
            }
            $updated = $this->dModel->dynamicUpdate(['id' => $id], Constants::TABLE_STATE, $updateStates);
            if (!$updated) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/edit-states-data/' . $id, 'Could not update state data'));
            }
            return redirect()->to($this->dModel->successReturn('/super-admin/show-states', 'States  Data data updated successfully!!!'));
        }
        $this->data['statesData'] = $statesData;
        $this->data['formUrl'] = BASE_URL . 'super-admin/edit-states-data/' . $id;
        return view('layout/super-admin/edit-states-data.php', $this->data);
    }
    public function addSuperAdminClients()
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $this->data['country'] = $this->dModel->dynamicCheckExist([], Constants::TABLE_COUNTRIES);
        $this->data['states'] = $this->schoolModel->selectStatesByParentId(Constants::TABLE_STATE, 'name', 'ASC', 0);
        $this->data['states'] = $this->statesTree();

        if ($this->request->getPost()) {
            $pData = $this->request->getPost();
            $phone = $this->dModel->validateEmailOrPhone($pData['contact_number']);
            $email = $this->dModel->validateEmailOrPhone($pData['email']);
            $result = $this->dModel->dynamicCheckExist(['client_name' => $pData['client_name']], Constants::TABLE_CLIENT);
            if ($result) {
                session()->setFlashdata('form_error', ['client_name' => 'Client Name already exist']);
                return redirect()->to(base_url() . '/super-admin/add-clients');
            }
            if (empty($phone) || empty($email)) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-clients', 'Email or Phone is not valid'));
            }
            $rules = [
                "client_name" => 'required|is_unique[client.client_name]',
                "client_title" => 'required',
                "email" => 'required',
                "contact_number" => 'required',
                "address" => 'required',
                "expired_at" => 'required',
                'country_id' => 'required',
                'district_id' => 'required',

            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());
                return redirect()->to(base_url('super-admin/add-clients'));
            }
            $socialLink = [
                'facebook'  => $pData['facebook'] ?? '',
                'youtube'   => $pData['youtube'] ?? '',
                'x'         => $pData['x'] ?? '',
                'instagram' => $pData['instagram'] ?? '',
            ];
            $cData = array(
                'client_name' => $pData['client_name'],
                'client_title' => $pData['client_title'],
                'email' => $email['content'],
                'contact_number' => $phone['content'],
                'description'    => $pData['description'],
                'address' => $pData['address'],
                'expired_at' => $pData['expired_at'],
                'country_id' =>  $pData['country_id'],
                'district_id' =>  $pData['district_id'],
                'google_map'     => $pData['google_map'],
                'client_type'    => $pData['client_type'],
                'social_link'    => json_encode($socialLink),

            );
            $cId = $this->dModel->dynamicInsertReturnId($cData, Constants::TABLE_CLIENT);
            if (!$cId) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-clients', 'Could not add clients data!!!'));
            }
            return redirect()->to($this->dModel->successReturn('/super-admin/add-clients', 'Clients data added successfully!!!'));
        }
        $this->data['formUrl'] = BASE_URL . 'super-admin/add-clients';
        return view('layout/super-admin/add-client-data', $this->data);
    }
    public function updateClient($id = null)
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $this->data['clientInfo'] = $this->dModel->dynamicCheckExistSingleRow(['id' => $id, 'is_active' => Constants::ACTIVE], Constants::TABLE_CLIENT);
        $this->data['states'] = $this->schoolModel->selectStatesByParentId(Constants::TABLE_STATE, 'name', 'ASC', 0);
        $this->data['countries'] = $this->dModel->dynamicCheckExist([], Constants::TABLE_COUNTRIES);
        if (is_null($this->data['clientInfo'])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/super-admin/view-clients');
        }
        if ($this->request->getPost()) {
            $postData = $this->request->getPost();
            $phone = $this->dModel->validateEmailOrPhone($postData['contact_number']);
            $email = $this->dModel->validateEmailOrPhone($postData['email']);
            $rules = [
                'client_name'    => 'required',
                'client_title'   => 'required',
                "country_id"     => 'required',
                "district_id"     => 'required',
                "description"    => 'required',
                "contact_number" => 'required',
                "email"          => 'required',
                "expired_at"     => 'required',
                "client_type"    => 'required',
            ];
            if (empty($phone) || empty($email)) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/edit-client/' . $id, 'Email or Phone is not valid'));
            }
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());
                return redirect()->to(rtrim(BASE_URL, '/') . '/super-admin/edit-client/' . $id);
            }
            $clientName = $postData['client_name'];
            $exist = $this->dModel->dynamicCheckExistSingleRow(['client_name' => $clientName], Constants::TABLE_CLIENT);
            if ($exist && $exist['id'] !== $id) {
                session()->setFlashdata('form_error', ['client_name' => 'Client Name already exist']);
                return redirect()->to(rtrim(BASE_URL, '/') . '/super-admin/edit-client/' . $id);
            }

            $socialLink = [
                'facebook'  => $postData['facebook'] ?? '',
                'youtube'   => $postData['youtube'] ?? '',
                'x'         => $postData['x'] ?? '',
                'instagram' => $postData['instagram'] ?? '',
            ];
            $value = [
                'country_id'     => $postData['country_id'],
                'district_id'    => $postData['district_id'],
                'client_name'    => $postData['client_name'],
                'client_title'   => $postData['client_title'],
                'description'    => $postData['description'],
                'contact_number' => $phone['content'],
                'client_type'    => $postData['client_type'],
                'email'          => $email['content'],
                'expired_at'     => $postData['expired_at'],
                'google_map'     => $postData['google_map'],
                'address'        => $postData['address'],
                'social_link'    => json_encode($socialLink),
            ];
            $insertData = $this->dModel->dynamicUpdate(['id' => $id], Constants::TABLE_CLIENT, $value);
            if (!$insertData) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/edit-hotel-info/' . $id, 'Could not  update client data!!!'));
            }
            return redirect()->to($this->dModel->successReturn('/super-admin/view-clients', 'Hotel  information updated successfully!!!'));
        }
        $this->parentId = $this->data['clientInfo']['district_id'];
        $this->data['states'] = $this->statesTree();
        $this->data['formUrl'] = BASE_URL . 'super-admin/edit-client/' . $id;
        return view('layout/super-admin/edit-client', $this->data);
    }
    public function superAdminAddUser()
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();

        if ($this->request->getPost()) {
            $pData = $this->request->getPost();
            if ($pData['password'] !== $pData['repass']) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-user', 'password dont match !!!'));
            }
            $validatePassword = $this->dModel->validatePassword($pData['password']);
            if ($validatePassword === false) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-user', 'Password has to be at least 8 characters, speacial character, number and string!!!'));
            }
            $password = password_hash($pData['password'], PASSWORD_BCRYPT);
            $email = $this->dModel->validateEmailOrPhone($pData['email']);
            $phone = $this->dModel->validateEmailOrPhone($pData['phone']);
            if (empty($email) || empty($phone)) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-user', 'Invalid email or phone !!!'));
            }
            $checkExistSuperAdmin = $this->dModel->dynamicCheckExist(['client_id' => Constants::DEACTIVE, 'email' => $email['content']], Constants::TABLE_USERS);
            $checkExistMerchant = $this->dModel->dynamicCheckExist(['client_id' => Constants::ACTIVE, 'email' => $email['content']], Constants::TABLE_USERS);
            if ($checkExistSuperAdmin ||   $checkExistMerchant) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-user', 'user already exist !!!'));
            }
            $rules = [
                "user_group"  => 'required',
                "name"      => 'required',
                "last_name"      => 'required',
                "password"   => 'required',
                "email"  => 'required',
                "phone"  => 'required',
                "start_date" => 'required',
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());
                return redirect()->to(rtrim(BASE_URL, '/') . '/super-admin/add-user');
            }
            if ($pData['password'] !== $pData['repass']) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-user', 'password dont match !!!'));
            }
            $softUsersData =  [
                'client_id' => $pData['client_id'] ?? 0,
                'name' => $pData['name'],
                'last_name' => $pData['last_name'],
                'email' => $email['content'],
                'phone' => $phone['content'],
                'present_address' => $pData['present_address'],
                'password' =>  $password,
                'b_date' =>  $pData['b_date'],
                'start_date' =>  $pData['start_date'],
                'end_date' =>  $pData['end_date'],
                'user_group' =>  $pData['user_group'],
                'op_id' => $userData['op_id'] ?? 0,
            ];
            $insertData = $this->dModel->dynamicInsertReturnId($softUsersData, Constants::TABLE_USERS);
            if (!$insertData) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/add-user', 'Could not insert user data'));
            }
            return redirect()->to($this->dModel->successReturn('/super-admin/add-user', 'Users data added successfully!!!', true));
        }
        $this->data['formUrl'] = BASE_URL . 'super-admin/add-user';
        return view('layout/super-admin/add-user', $this->data);
    }
    public function superAdminUpdateUser($id)
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $data = $this->dModel->dynamicCheckExistSingleRow(['id' => $id], Constants::TABLE_USERS);
        if (is_null($data)) {
            return redirect()->to($this->dModel->falseReturn('/super-admin/view-user', 'User data not found!!!'));
        }
        $this->data['client'] = $this->dModel->dynamicCheckExist([], Constants::TABLE_CLIENT);
        if ($this->request->getPost()) {
            $pData = $this->request->getPost();
            $phone = $this->dModel->validateEmailOrPhone($pData['phone']);
            if (empty($phone)) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/edit-user/' . $id, 'Phone is not valid!!!'));
            }
            $rules = [
                "user_group"  => 'required',
                "name"      => 'required',
                "last_name"      => 'required',
                "phone"  => 'required',
                "start_date" => 'required',

            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());
                return redirect()->to(rtrim(BASE_URL, '/') . '/super-admin/edit-user/' . $id);
            }
            $cData = array(
                'name'      => $pData['name'],
                'last_name'      => $pData['last_name'],
                'phone'          => $phone['content'],
                'start_date'     => $pData['start_date'],
                'end_date'       => $pData['end_date'],
                'b_date'       => $pData['b_date'],
                'user_group' => $pData['user_group'],
                'client_id' => $pData['client_id'] ?? 0,
                'present_address' => $pData['present_address'],
            );

            $updated = $this->dModel->dynamicUpdate(['id' => $id], Constants::TABLE_USERS, $cData);
            if (!$updated) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/edit-user/' . $id, 'Could not update users data!!!'));
            }
            return redirect()->to($this->dModel->successReturn('/super-admin/view-user', 'User data updated successfully!!!'));
        }
        $this->data['formUrl'] = BASE_URL . 'super-admin/edit-user/' . $id;
        $this->data['data'] =   $data;
        return view('layout/super-admin/edit-user', $this->data);
    }
    public function changePassword()
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        if ($this->request->getPost()) {
            $pData = $this->request->getPost();
            $rules = [
                "password" => 'required',
                "repass" => 'required',
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('form_error', $this->validator->getErrors());
                return redirect()->to($this->dModel->falseReturn('/super-admin/change-password', ''));
            }
            if ($pData['password'] != $pData['repass']) {
                return redirect()->to($this->dModel->successReturn('/super-admin/change-password',  'Password doesn\'t match!!!'));
            }
            $validatePassword = $this->dModel->validatePassword($pData['password']);
            if ($validatePassword === false) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/change-password', 'Password has to be at least 8 characters, speacial character, number and string!!!'));
            }
            $password = password_hash($pData['password'], PASSWORD_BCRYPT);
            $pId = $this->dModel->dynamicUpdate(['id' => $userData['op_id']], Constants::TABLE_USERS, ['password' => $password]);
            if (!$pId) {
                return redirect()->to($this->dModel->falseReturn('/super-admin/change-password',  'Change Password Failed!!!'));
            }
            return redirect()->to($this->dModel->successReturn('/super-admin/change-password',  'Change Password Successfull!!!'));
        }
        $this->data['formUrl'] = BASE_URL . 'super-admin/change-password';
        return view('layout/super-admin/change-password', $this->data);
    }
    public function addArea()
    {

        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if (empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if (!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        $this->data['area'] = $this->dModel->dynamicCheckExist(['parent_id' => Constants::PARENT_ID], Constants::TABLE_STATE);
      
       
        $this->data['formUrl'] = BASE_URL . 'super-admin/add-state';
        $this->data['states'] = $this->statesTree();
        return view('layout/super-admin/add-area', $this->data);
    }

    public function getUpozila()
    {
        $parentId = $_REQUEST['id'];
        if (!empty($parentId)) {
            $upozila= $this->dModel->dynamicCheckExist(['parent_id' =>   $parentId, ], Constants::TABLE_STATE);
        }
       
        $upozilaHtml = '';
        $upozilaHtml .= " <label for='' class='form-label'>Upozila</label>";
        $upozilaHtml .= " <select  class='form-select mb-3' name='upozila_id' id='upozila_id' aria-label='Default select example'>";
        $upozilaHtml .= "  <option value=''>Select upozila</option>";
        if (!is_null($upozila)) {
            foreach ($upozila as  $value) {
                 $upozilaHtml .= "<option value=" . $value['id'] . ">" . $value['name'] . "</option>";
            }
        }

        $upozilaHtml .= "</select>";
        $data['upozilaHtml'] = $upozilaHtml;
        $jsonData = json_encode($data);
        return $this->response->setBody($jsonData)->setContentType('application/json');
    }
}
