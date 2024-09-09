<?php

namespace App;

class Company {
    function getCompanyList($data){
        $companyRep = new CompanyRepository();
        $user = new User();
        $user_id = $user->getUserIdByToken($data["token"]);
        $companyList = $companyRep->getCompanyList($user_id);
        $result = array();
        if (isset($companyList["error"])){
            $result = [
                "status" => "fail",
                "company_list" => null,
                "error" => $companyList["error"],
            ];
           
        }else{
            $result = [
            "status" => "success",
            "company_list" => $companyList["list"],
            ];
    }
    echo json_encode($result);
}
public function addCompany($data)
{
    $companyRepo = new CompanyRepository();
    $company_id = $companyRepo->addCompany($data);
    $result =array();
    if ($company_id != null) {
        $result = [
            "status" => "success",
            "company_id" => $company_id,
        ];
    }else{
        $result = [
            "status" => "fail",
            "company_id" => null,
            "error" => "can`t create company",
        ];
    }

}
public function deleteCompany($data){}
}



