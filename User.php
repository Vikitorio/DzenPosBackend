<?php

class User
{
    private $phone;
    private $password;
    private $name;
    private $surname;
    private $array;
    public function __construct($data_array){
        $this->phone = $data_array->phone;
        $this->password = $data_array->password;
        $this->name = $data_array->name;
        $this->surname = $data_array->surname;
    }
private $_id;
public function registration(){
    $db = new DBConnection();
    $db->createAccount($this->phone,$this->password,$this->name,$this->surname);
}
public function getUserInfo(){
     echo  $this->phone.'-'.$this->password.'-'.$this->name.'-'.$this->surname;
    }
}