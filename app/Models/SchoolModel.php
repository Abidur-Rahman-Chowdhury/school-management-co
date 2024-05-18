<?php namespace App\Models;
use App\Constant\Constants;
use App\Constant\DaysName;
use CodeIgniter\Model;
class SchoolModel extends Model
{
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function selectStatesByParentId(string $table, string $orderById, string $ascDesc, int $parentId)
    {
        $query = $this->db->table($table)->where('parent_id', $parentId);
        $query->orderBy($orderById, $ascDesc);
        return $query->select()->get()->getResultArray();
    }
    public function selectAllStates()
    {
        $query = $this->db->table('state')
        ->join('state as state2', 'state2.id = state.parent_id', 'LEFT');
        return $query->select('state.*, state2.name as parent_name')->get()->getResultArray();
    }
    public function getClientInformation($data = null)
    {
        $currentDate = date('Y-m-d');
        $query = $this->db->table('client')
            ->select('client.id, client.client_name,  client.is_active,  client.expired_at, client.created_at, client.client_title, client.description, client.logo, client.contact_number, client.email,client.client_type, state.id as state_id, state.name as district_name, countries.country_id, countries.short_name as country_name')
            ->join('state', 'state.id = client.district_id', 'LEFT')
            ->join('countries', 'countries.country_id = client.country_id', 'LEFT')
            ->where('client.expired_at >', $currentDate);
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $query->where('client.id', $data['user_id']);
        }

        if (isset($data['client_id']) && !empty($data['client_id'])) {
            $query->where('client.id', $data['client_id']);
        }
        if (isset($data['area_id']) && !empty($data['area_id'])) {
            $query->where('area.id', $data['area_id']);
        }
        if ((isset($data['from']) && !empty($data['from'])) && isset($data['to']) && !empty($data['to'])) {
            $from = date('Y-m-d H:i:s', strtotime($data['from']));
            $to = date('Y-m-d 23:59:59', strtotime($data['to']));
            $query->where('client.created_at >=', $from);
            $query->where('client.created_at <=', $to);
        }
        return $query->get()->getResultArray();
    }
    public function getClientName($val, $id)
    {
        $query = $this->db->table('client c')
            ->select('c.id, c.client_name')
            ->where(" c.client_name LIKE '%" . strtolower($val) . "%'")
            ->limit(10)
            ->get();
        return $query->getResultArray();
    }

    public function getClientEmail($val, $id)
    {
        $query = $this->db->table('client c')
            ->select('c.id, c.email')
            ->where(" c.email LIKE '%" . strtolower($val) . "%'")
            ->limit(10)
            ->get();
        return $query->getResultArray();
    }
    public function getUserEmail($val, $id)
    {
        $query = $this->db->table('users  su')
            ->select('su.id, su.email')
            ->where(" su.email LIKE '%" . strtolower($val) . "%'")
            ->limit(10)
            ->get();
        return $query->getResultArray();
    }
 
    public function getClientPhone($val, $id)
    {
        $query = $this->db->table('client c')
            ->select('c.id, c.contact_number')
            ->where(" c.contact_number LIKE '%" . $val . "%'")
            ->limit(10)
            ->get();
        return $query->getResultArray();
    }
    public function getUserPhone($val, $id)
    {
        $query = $this->db->table('users  su')
            ->select('su.id, su.phone')
            ->where(" su.phone LIKE '%" . $val . "%'")
            ->limit(10)
            ->get();
        return $query->getResultArray();
    }
   
    public function getSuperAdminUsers($data)
    {
      
        $query = $this->db->table('users su')
            ->select('su.id, su.phone, su.client_id, su.name, su.last_name, su.b_date,  su.email, su.phone,  su.user_group, su.is_active, su.present_address, su.start_date, su.created_at, su.end_date,  client.client_name')
            ->join('client', 'client.id = su.client_id', 'left')
            ->whereIn('su.user_group', Constants::USER_GROUP);
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $query->where('su.id', $data['user_id']);
        }
        if (isset($data['client_id']) && !empty($data['client_id'])) {
            $query->where('su.client_id', $data['client_id']);
        }
       
        if (isset($data['user_group']) && !empty($data['user_group'])) {
            $query->where('su.user_group', $data['user_group']);
        }
        if (isset($data['is_active'])  && $data['is_active'] != '') {
            $query->where('su.is_active', (int) $data['is_active']);
        }
        if ((isset($data['from']) && !empty($data['from'])) && isset($data['to']) && !empty($data['to'])) {
            $from = date('Y-m-d H:i:s', strtotime($data['from']));
            $to = date('Y-m-d 23:59:59', strtotime($data['to']));
            $query->where('su.created_at >=', $from);
            $query->where('su.created_at <=', $to);
        }
        return $query->get()->getResultArray();
    }

}
