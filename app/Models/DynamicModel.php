<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Constant\Constants;
use DateTime;
use PHPUnit\TextUI\XmlConfiguration\Constant;

class DynamicModel extends Model
{
    protected $db;
    function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function dynamicInsert(array $data, string $table)
    {
        $query = $this->db->table($table);
        return $query->insert($data);
    }
    public function selectAllData(string $table, string $orderById, string $ascDesc)
    {
        $query = $this->db->table($table);
        $query->orderBy($orderById, $ascDesc);
        $query->where('is_active', 1);
        return $query->select()->get()->getResultArray();
    }
    public function showAllData(string $table, string $orderById, string $ascDesc)
    {
        $query = $this->db->table($table);
        $query->orderBy($orderById, $ascDesc);
        return $query->select()->get()->getResultArray();
    }
    public function dynamicInsertReturnId(array $data, string $table)
    {
        $this->db->table($table)->insert($data);
        return $this->db->insertID();
    }
    public function dynamicCheckExist(array $where, string $table)
    {
        $query = $this->db->table($table);
        $query->where($where);
        return $query->select()->get()->getResultArray();
    }
    public function dynamicCheckExistSingleRow(array $where, string $table)
    {
        $query = $this->db->table($table);
        $query->where($where);
        $result = $query->select()->get()->getRowArray();
        if (!empty($result)) {
            return $result;
        }
        return null;
    }
    public function dynamicCheckExistSingleLastRow(array $where, string $table)
    {
        $query = $this->db->table($table);
        $query->where($where);
        $result = $query->select()->get()->getLastRow();
        if (!empty($result)) {
            return get_object_vars($result);
        }
        return null;
    }
    public function getSlideImages()
    {
        $query = $this->db->table('softsol_data')
            ->select('images.file_name, softsol_data.page_title, softsol_data.description')
            ->join('images', 'softsol_data.id = images.page_id')
            ->where('softsol_data.page_name', 'slide')
            ->where('images.is_active', 1)
            ->get();
        return $query->getResultArray();
    }
    public function dynamicUpdate(array $where, string $table, array $data)
    {
        $query = $this->db->table($table);
        $query->where($where);
        return $query->update($data, $where);
    }

    /* genarate invoiceid */
    public function generateInvoice($length = 16)
    {
        return 'KLH' . strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length));
    }
    public function imageUpload($FILES, $folderPath = '', $targetWidth = 200, $targetHeight = 200, $thumbnail = false)
    {
        $file = $FILES['tmp_name'];
        $sourceProperties = getimagesize($file);

        if (empty($folderPath)) {
            $folderPath = "test-img/";
        }

        $thumbnailPath = 'assets/file/thumbnail/';
        $ext = pathinfo($FILES['name'], PATHINFO_EXTENSION);
        $fileNewName = "cnf" . '-' . time() . '.' . $ext;
        $imageType = $sourceProperties[2];
        switch ($imageType) {

            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($file);
                $targetLayer = $this->imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1], $targetWidth, $targetHeight);
                imagepng($targetLayer, $folderPath . $fileNewName);
                if ($thumbnail) {
                    $targetLayer = $this->thumbnail($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                    imagepng($targetLayer, $thumbnailPath . $fileNewName);
                }
                return $fileNewName;

            case IMAGETYPE_GIF:
                $imageResourceId = imagecreatefromgif($file);
                $targetLayer = $this->imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1], $targetWidth, $targetHeight);
                imagegif($targetLayer, $folderPath . $fileNewName);
                if ($thumbnail) {
                    $targetLayer = $this->thumbnail($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                    imagepng($targetLayer, $thumbnailPath . $fileNewName);
                }
                return $fileNewName;

            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($file);
                $targetLayer = $this->imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1], $targetWidth, $targetHeight);
                imagejpeg($targetLayer, $folderPath . $fileNewName);
                if ($thumbnail) {
                    $targetLayer = $this->thumbnail($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                    imagepng($targetLayer, $thumbnailPath . $fileNewName);
                }
                return $fileNewName;

            default:
                return "Invalid Image type.";
        }
    }
    function imageResize($imageResourceId, $width, $height, $targetWidth, $targetHeight)
    {
        $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        return $targetLayer;
    }
    function thumbnail($imageResourceId, $width, $height)
    {
        $targetWidth = Constants::THUMBNAIL_WEIGHT;
        $targetHeight = Constants::THUMBNAIL_HEIGHT;
        $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        return $targetLayer;
    }
    public function trackingID($length = 5)
    {
        return 'KLH' . strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length));
    }
    public function methodStartSession()
    {
        $fmsg = '';
        $status = '';
        if (session()->has('sstatus')) {
            $fmsg = session()->get('sfmsg');
            $status = session()->get('sstatus');
        }
        session()->remove('sfmsg');
        session()->remove('sstatus');
        return [
            $fmsg,
            $status
        ];
    }
    public function falseReturn($route, $message, $rollback = false)
    {
        if ($rollback) {
            $this->db->transRollback();
        }
        $ssData = ['sfmsg' => $message, 'sstatus' => 'red'];
        session()->set($ssData);
        return rtrim(base_url(), '/') . $route;
    }

    public function successReturn($route, $message, $commit = false)
    {
        if ($commit) {
            $this->db->transCommit();
        }
        $ssData = ['sfmsg' => $message, 'sstatus' => 'green'];
        session()->set($ssData);
        return rtrim(base_url(), '/') . $route;
    }
    public static function validateEmailOrPhone($content): ?array
    {
        if (filter_var($content, FILTER_VALIDATE_EMAIL)) {
            return [
                'type' => 'email',
                'content' => $content
            ];
        }

        if (preg_match("/(^(\+8801|8801|01|1))[1|3-9]{1}(\d){8}$/", $content)) {

            $phone = self::formatPhone($content);
            return [
                'type' => 'phone',
                'content' => $phone
            ];
        }

        return [];
    }
    public static function formatPhone($phone)
    {
        return "880" . substr($phone, -10);
    }
    function validatePassword($password)
    {
        $hasSpecialChar = preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:\'",.<>\/?]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasString = preg_match('/[a-zA-Z]/', $password);
        $isMinLength = strlen($password) >= 8;
        if ($hasSpecialChar && $hasNumber && $hasString && $isMinLength) {
            return true;
        } else {
            return false;
        }
    }
    public function dayDifference($checkingDate, $checkoutDate)
    {
        $date1 = new DateTime($checkingDate);
        $date2 = new DateTime($checkoutDate);
        return ($date1->diff($date2))->days;
    }
}
