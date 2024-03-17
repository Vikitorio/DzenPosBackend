<?php

class User
{
    private $phone;
    private $password;
    private $name;
    private $surname;
    public function __constructor($phone,$password,$name=null,$surname=null){
        $this->phone = $phone;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
    }
private $_id;
public function registration(){
    $db = new DBConnection();
    $db->createAccount($this->phone,$this->password,$this->name,$this->surname);
}
public function getUserInfo(){
     return  $this->name.$this->phone;
    }
}