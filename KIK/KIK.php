<?php
namespace KIK;

class KIK extends \Table\Base
{

    protected $_rawData;
    protected $_decoded;
    protected $_date;
    protected $_connectedWith;
    protected $_userID;
    protected $_userName;
    protected $_accounts;
    protected $_cList;
    protected $_zap;

    public function __construct($options)
    {
        parent::__construct($options);
        $this->_decoded = \json_decode($this->_rawData, true);
       /*  echo $this->_rawData . "<br><br>"; */
    }

    public function renderMainInfo()
    {
        $template = new \Tegs\Template(array("_template" => "KIK/templates/main_info.html.tegs"));

        if (!empty($this->_connectedWith)) {
            $connectedWith = new KIK(array("_rawData" => $this->_connectedWith[0]["content"]));
            $connectedWith = $connectedWith->getFullName();
        }
        else $connectedWith = "Brak";
        $scope = array(
            "connectedWith" => $connectedWith,
            "type" => self::getType(),
            "date" => $this->_date,
            "fullname" => $this->_userName,
            "connectedWithID"=>(isset($this->_connectedWith[0]["id"]))?$this->_connectedWith[0]["id"]:"",
            "userID"=>$this->_userID,
            "purpose" => $this->_decoded["purpose"],
            "amount" => $this->_decoded["amount"],
            "comment" => $this->_decoded["comment"],
            "accounts"=>$this->_accounts,
            "cList"=>$this->_cList,
            "zap"=>!$this->_zap,
        );
        return $template->render($scope);
    }
    public function renderKIK()
    {
        $personal = $financial = $debts = [];
        $template = new \Tegs\Template(array("_template" => "KIK/templates/kik.html.tegs"));

        $first_bp = array_search('tax-residence', array_keys($this->_decoded));
        if ($first_bp === false) {
            $first_bp = array_search('branch', array_keys($this->_decoded));
        }
        $second_bp = array_search('asset-type-0', array_keys($this->_decoded));
        $third_bp = array_search('purpose', array_keys($this->_decoded));
        $first = array_slice($this->_decoded, 0, $first_bp);
        $second = array_slice($this->_decoded, $first_bp, $second_bp - $first_bp);
        $third = array_slice($this->_decoded, $second_bp, $third_bp - $second_bp);
        $fourth = array_slice($this->_decoded, $third_bp);

        foreach ($first as $key => $value) {
            $personal[] = array(
                "org"=>$key,
                "name" => $this->replaceKey($key),
                "value" => $this->replaceValue($value)
            );
        }
        foreach ($second as $key => $value) {
            $financial[] = array(
                "org"=>$key,
                "name" => $this->replaceKey($key),
                "value" => $this->replaceValue($value)
            );
        }
        foreach ($third as $key => $value) {
            $debts[] = array(
                "org"=>$key,
                "name" => $this->replaceKey($key),
                "value" => $this->replaceValue($value)
            );
        }
        $scope = array(
            "personal"=>$personal,
            "financial"=>$financial,
            "debts"=>$debts
        );
        return $template->render($scope);
    }

    private function getType()
    {
        $data = $this->_decoded;
        if (\is_array($data) && \array_key_exists("fullname-1", $data)) {
            return "osoba fizyczna";
        }
        if (\is_array($data) && \array_key_exists("fullname-1-comp", $data)) {
            return "firma";
        }
    }

    public function getFullName()
    {
        $data = $this->_decoded;
        if (\is_array($data) && \array_key_exists("fullname-1", $data)) {
            return $data["fullname-1"];
        }
        if (\is_array($data) && \array_key_exists("fullname-1-comp", $data)) {
            return $data["fullname-1-comp"];
        }
        return "Niewłaściwy format danych";
    }

    private function replaceKey($key)
    {
        require ("KIK_names.php");

        $key_r = $key;
        foreach ($names as $a => $b) {
            if (strpos($key_r, $a) !== false) {
                $key = $b;
            }
            if ($key_r === "") {
                $key = "-";
            }
        }
        return $key;
    }

    private function replaceValue($value)
    {
        require ("KIK_names.php");

        $value_r = $value;
        foreach ($values as $a => $b) {
            if (strpos($value_r, $a) !== false) {
                $value = $b;
            }
            if ($value_r === "") {
                $value = "-";
            }
        }
        if (!strcmp($value_r, "0")) {
            $value = "TAK";
        }
        if (!strcmp($value_r, "1")) {
            $value = "NIE";
        }
        return $value;
    }
}
