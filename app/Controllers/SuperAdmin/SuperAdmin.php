<?php
namespace App\Controllers\SuperAdmin;
use App\Constant\Constants;
use App\Controllers\BaseController;
use App\Models\SchoolModel;
use App\Models\DynamicModel;

class SuperAdmin extends BaseController
{
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
    public function index()
    {
       
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
       
        if(empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        
        if(!in_array($userGroup, [Constants::USER_GROUP[0]]) ) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        } 
        return view('layout/super-admin/super-admin-home');
    }
    public function showStatesInformation()
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if(empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if(!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        } 
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $this->data['data'] = $this->schoolModel->selectAllStates();
        return view('layout/super-admin/show-states-data.php', $this->data);
    }
    public function showClient()
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if(empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if(!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        } 
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $pdata = $this->request->getPost();
        $this->data['data'] =   $this->schoolModel->getClientInformation($pdata);
        $this->data['formUrl'] = BASE_URL . 'super-admin/view-clients';
        return view('layout/super-admin/show-client', $this->data);
    }
    public function getClientName()
    {
        $str = '';
        $val         = strtolower($_REQUEST['val']);
        $email         = strtolower($_REQUEST['id']);
        $getItem = $this->schoolModel->getClientName($val, null);
        for ($i = 0; $i < sizeof($getItem); $i++) {
            $id                 = $getItem[$i]['id'];
            $clientName                 = $getItem[$i]['client_name'];
            $str .= "<div align='left' onClick=\"fill_client_id_by_tanent('" . $id . "', '" . $clientName . "');\"><b>" . $clientName . "</b></div>";
        }
        echo $str;
        exit;
    }
    public function getClientEmail()
    {
        $str = '';
        $val         = strtolower($_REQUEST['val']);
        $email         = strtolower($_REQUEST['id']);
        $getItem = $this->schoolModel->getClientEmail($val, null);
        for ($i = 0; $i < sizeof($getItem); $i++) {
            $id                 = $getItem[$i]['id'];
            $email                 = $getItem[$i]['email'];
            $str .= "<div align='left' onClick=\"fill_email_id_by_tanent('" . $id . "', '" . $email . "');\"><b>" . $email . "</b></div>";
        }
        echo $str;
        exit;
    }
    public function getClientPhone()
    {
        $str = '';
        $val         = strtolower($_REQUEST['val']);
        $email         = strtolower($_REQUEST['id']);
        $getItem = $this->schoolModel->getClientPhone($val, null);
        for ($i = 0; $i < sizeof($getItem); $i++) {
            $id                 = $getItem[$i]['id'];
            $phone                 = $getItem[$i]['contact_number'];
            $str .= "<div align='left' onClick=\"fill_phone_id_by_tanent('" . $id . "', '" . $phone . "');\"><b>" . $phone . "</b></div>";
        }
        echo $str;
        exit;
    }
    public function superAdminShowUsers()
    {
        $userGroup = session()->get('user_group');
        $userData = session()->get($userGroup);
        if(empty($userGroup)) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        }
        if(!in_array($userGroup, [Constants::USER_GROUP[0]])) {
            return redirect()->to(rtrim(BASE_URL, '/') . '/login');
        } 
        [$this->data['fmsg'], $this->data['status']] = $this->dModel->methodStartSession();
        $sData = $this->request->getPost();
        $this->data['data'] = $this->schoolModel->getSuperAdminUsers($sData);
        $this->data['formUrl'] = BASE_URL . 'super-admin/view-user';
        return view('layout/super-admin/show-users', $this->data);
    }
    public function getUserEmail()
    {
        $str = '';
        $val         = strtolower($_REQUEST['val']);
        $email         = strtolower($_REQUEST['id']);
        $getItem = $this->schoolModel->getUserEmail($val, null);
        for ($i = 0; $i < sizeof($getItem); $i++) {
            $id                 = $getItem[$i]['id'];
            $email                 = $getItem[$i]['email'];
            $str .= "<div align='left' onClick=\"fill_email_id_by_tanent('" . $id . "', '" . $email . "');\"><b>" . $email . "</b></div>";
        }
        echo $str;
        exit;
    }
    public function getUserPhone()
    {
        $str = '';
        $val         = strtolower($_REQUEST['val']);
        $email         = strtolower($_REQUEST['id']);
        $getItem = $this->schoolModel->getUserPhone($val, null);
        for ($i = 0; $i < sizeof($getItem); $i++) {
            $id                 = $getItem[$i]['id'];
            $phone                 = $getItem[$i]['phone'];
            $str .= "<div align='left' onClick=\"fill_phone_id_by_tanent('" . $id . "', '" . $phone . "');\"><b>" . $phone . "</b></div>";
        }
        echo $str;
        exit;
    }
    public function superAdmindeleteData($route = null, $id = null, $delValue = null, $table = null, $backRoutue = null)
    {
      
        $this->dModel->dynamicUpdate(['id' => $id,], $table, ['is_active' => $delValue]);
    }
    public function superAdminActDecData($route = null, $id = null, $actDec = null, $table = null, $backRoutue = null)
    {   
    
        $this->dModel->dynamicUpdate(['id' => $id], $table, ['is_active' => $actDec]);
       
    }
}
