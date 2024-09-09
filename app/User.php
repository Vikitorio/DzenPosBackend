<?php
namespace App;
class User
{
    private $sessionToken;
    public function authorization($data)
    {
        $userRep = new UserRepository();
        $result = [];
        $token = $userRep->authorization($data);
        if ($token) {
            $result = [
                "status" => "success",
                "token" => $token,
            ];
        } else {
            $result = [
                "status" => "failure",
                "error" => "Invalid data"
            ];
        }
    
        echo json_encode($result);
    }
    public function registration($data)
    {
        $userRep = new UserRepository();
        $userData = [
            "phone_number" => $data["phone_number"],
            "password" => $data["password"],
            "name" => $data["name"],
            "surname" => $data["surname"],
        ];
        $response = array(
            "status" => "",
            "error" => "",

        );
        if ($userRep->isAccountExist($userData["phone_number"])) {
            $response["status"] = "error";
            $response["status_code"] = "0";
            $response["error"] = "Account already exists";
        } else {
            $result = $userRep->createAccount($userData);
            if ($result === true) {
                $response["status"] = "success";
            } else {
                $response["status"] = "error";
                $response["error"] = $result;
            }
        }
        if ($response["status"] === "success") {
            $response["token"] = $userRep->registrateSession($userData["phone_number"]);
        }
        echo json_encode($response);
    }


   
    public function getUserIdByToken($token)
    {
        $userRep = new UserRepository();
        $userId = $userRep->getUserIdByToken($token);
        return $userId;
    }

   


}

