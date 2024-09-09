<?php
namespace App;

class CompanyRepository extends Repository
{
    function getCompanyList($id)
    {
        try {
            $stmt = $this->dbConnection->prepare("SELECT * FROM company WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $id);
            $stmt->execute();
            $data = array();
            $i = 0;
            while ($row = $stmt->fetch()) {
                $data["list"][$i]["company_id"] = $row["id"];
                $data["list"][$i]["company_name"] = $row["company_name"];
                $data["list"][$i]["company_adress"] = $row["address"];
                $i++;
            }
            return $data;
        } catch (PDOException $e) {
            $data["error"] = $e->getMessage();
            return $data;
        }
    }
    public function addCompany($data)
    {
        $user = new User();
        $user_id = $user->getUserIdByToken($data["token"]);
        $company_id = uniqid("", false);
        $company_data = $data["company_data"];
        $company_data["user_id"] = $user_id;
        $company_data["id"] = $company_id;
        echo $user_id . " company " . $company_id;
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO company (id, user_id, company_name,address) VALUE (:id,:user_id, :title,:address)");
            $stmt->execute($company_data);
            return  $company_id;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}