<?php

defined("LOKI") || die("you no have right to access here");
interface IValidator {

    public function validate($str);

    public function getMessage();
}

class ValidateFunc implements IValidator {

    private $Message;
    private $Func;
    private $para;

    public function __construct($func,$para, $message) {
        $this->Func = $func;
        $this->Message = $message;
        $this->para=$para;
    }

    public function getMessage() {
        return $this->Message;
    }

    public function validate($str) {
         return call_user_func_array($this->Func, $this->para);
    }

}

class Validate_Req implements IValidator {

    private $Message;

    public function __construct($message) {
      
        $this->Message = $message;
         
    }

    public function getMessage() {
        return $this->Message;
    }

    public function validate($str) {
        if (is_string($str)) {
            $tmp = trim($str);
            return $tmp != "";
        }

        if (is_array($str)) {
            if (empty($str))
                return FALSE;
            foreach ($str as $value) {
                if (trim($value) == "") {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

}

class Validate_Regex implements IValidator {

    private $Expr;
    private $Message;

    public function __construct($expr, $message) {
        $this->Expr = $expr;
        $this->Message = $message;
    }

    public function getMessage() {
        return $this->Message;
    }

    public function validate($str) {
        if($str=='')return TRUE;
        
        if (is_string($str)) {
            return preg_match($this->Expr, $str)==1;
        }

        if (is_array($str)) {
            foreach ($str as $value) {
                if (!preg_match($this->Expr, $value)) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

}

class Validate_Email extends Validate_Regex {

    public function __construct(string $message) {
        $expr = '/^.+@[^.].*\.[a-z]{2,10}$/i';
        parent::__construct($expr, $message);
    }

}

class Validate_URL extends Validate_Regex {

    public function __construct(string $message) {
        $expr = '/^(http|https)?://([^/:]+|([0-9]{1,3}\.){3}[0-9]{1,3})(:[0-9]+)?(\/.*)?$/i';
        parent::__construct($expr, $message);
    }

}

class Validate_MustDigit extends Validate_Regex {

    public function __construct($message) {
        $expr = '/^\d+$/';
        parent::__construct($expr, $message);
    }

}

class Validate_Mobile extends Validate_Regex {

    public function __construct(string $message) {
        $expr = '/^13[0-9]{1}[0-9]{8}$|15[013589]{1}[0-9]{8}$|189[0-9]{8}$/';
        parent::__construct($expr, $message);
    }

}

class Validate_MinWidth implements IValidator {

    private $Message;
    private $Length;

    public function __construct($len, $message) {
        $this->Message = $message;
        $this->Length = $len;
    }

    public function getMessage() {
        return $this->Message;
    }

    public function validate($str) {
         if($str=='') return TRUE;
        if (is_string($str)) {
            return strlen($str) >= $this->Length;
        }

        if (is_array($str)) {
            foreach ($str as $value) {
                if (strlen($value) < $this->Length) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

}

class Validate_Equal implements IValidator {

    private $Message;
    private $value;

    public function __construct($val, $message) {
        $this->Message = $message;
        $this->value = $val;
    }

    public function getMessage() {
        return $this->Message;
    }

    public function validate($str) {
        return $this->value == $str;
    }

}

class Validate_Unique implements IValidator {

    private $Message;

    public function __construct($message) {
        $this->Message = $message;
    }

    public function getMessage() {
        return $this->Message;
    }

    public function validate($arr) {
        if (is_array($arr)) {
            $count = count($arr);
            $tmp = array_unique($arr);
            if ($count == count($tmp)) {
                return true;
            }
            return FALSE;
        }

        return true;
    }

}

class Form {

    private $tmpName;
    private $val;
    private $err;
    private $checkToken;

    public function __construct() {
        $this->val = array();
        $this->tmpName = "";
        $this->err = array();
        $this->checkToken = FALSE;
    }

    public function Token() {
        $token = md5(date('Ymd H:i:s') . rand(1, 5000));
        Session::set('token', $token) ;
        echo "<input type='hidden' name='token' value='$token'>";
        $this->checkToken = true;
    }

    public function GetErrMessage() {
        return $this->err;
    }

    public function Name($name) {
        if (!array_key_exists($name, $this->val)) {
            $this->val[$name] = array();
        }
        $this->tmpName = $name;

        return $this;
    }

    public function Value($name) {
        if( $_SERVER['REQUEST_METHOD']=='GET'){
            return Context::Get($name);
        }
        return Context::Post($name);
    }

    private function addValidator($vali) {
        $arr = &$this->val[$this->tmpName];
        $arr[] = $vali;
        return $this;
    }

    public function Req($message) {
        return $this->addValidator(new Validate_Req($message));
    }

    public function URL($message) {
		return $this->addValidator(new Validate_URL($message));
    }

    public function Func($func, $message,$para=array()) {
        return $this->addValidator(new ValidateFunc($func,$para, $message));
    }

    public function Regex($expr, $message) {
        return $this->addValidator(new Validate_Regex($expr, $message));
    }

    public function Email($message) {
        return $this->addValidator(new Validate_Email($message));
    }

    public function Digdit($message) {
        return $this->addValidator(new Validate_MustDigit($message));
    }

    public function Mobile($message) {
        return $this->addValidator(new Validate_Mobile($message));
    }

    public function MinWidth($width, $message) {
        return $this->addValidator(new Validate_MinWidth($width, $message));
    }

    public function Equal($val, $message) {
        return $this->addValidator(new Validate_Equal($val, $message));
    }
    
    public function Unique($message){
        return $this->addValidator(new Validate_Unique($message));
    }

    public function Valid() {

        if ($this->checkToken && Session::get('token')!=NULL && $this->Value('token') != Session::get('token')) {
            $this->err['message'] = '请不要重复提交';
            return FALSE;
        }
        Session::del('token');
        $ret = true;
        foreach ($this->val as $key => $arr) {
            $value = $this->Value($key);
            foreach ($arr as $validate) {
                $result = $validate->validate($value);
                if (!$result) {
                    $this->err[$key] = $validate->getMessage();
                    $ret = false;
                    break;
                }
            }
        }
        return $ret;
    }

}

?>
