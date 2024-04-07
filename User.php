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
        $this->name = isset($data_array->name) ? $data_array->name : null;
        $this->surname = isset($data_array->surname) ? $data_array->surname : null;
    }
    public function user_login(){
        $db = new DBConnection();
        $db->authorization($this->phone,$this->password);
    }
    public function registration(){
    $db = new DBConnection();
    $db->createAccount($this->phone,$this->password,$this->name,$this->surname);
}
    public function getUserInfo(){
     echo  $this->phone.'-'.$this->password.'-'.$this->name.'-'.$this->surname;
    }

}