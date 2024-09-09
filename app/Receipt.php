<?php
namespace App;

class Receipt {
    public function makeCheck($data){
        $checkData = $data["check"];
        $checkData["company_id"] = $data["company_id"];
        $receiptRepo = new ReceiptRepository();
        $receiptId = $receiptRepo->makeCheck($checkData);

        $product = new Product();
        if($receiptId !== null){
            $checkData["document_id"] = $receiptId;
            if($product->makeProductSale($checkData)){
                $result = array();
                $result["status"]="done";
                echo json_encode($result);
            }
            
        }
       
        //$this->makeSaleRecords($lastId,$company, $data["products"] );
        
    }
}

?>