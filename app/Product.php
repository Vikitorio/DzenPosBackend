<?php

namespace App;
class Product
{
    public function addProduct($userId, $data) {
        try {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['image'];
                $companyId = $data["company_id"];
                $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/images/ProductImages/' . $companyId . '/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $targetFile = $targetDir . basename($image['name']);
                if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                    $imageSrc = '/images/ProductImages/' . $companyId . '/' . basename($image['name']);
                } else {
                    throw new Exception('Failed to upload the image.');
                }
            } else {
                $imageSrc = null;
            }
            $data["image_src"] = $imageSrc;
            $pr_rep = new ProductRepository();
            $result = array();
            $result["status"] = $pr_rep->addProduct($data);
            echo json_encode($result);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
    public function updateQuantity($data){
        try {
            $pr_rep = new ProductRepository();  
            $result = $pr_rep->updateQuantity($data);
        } catch (PDOException $e) {}
    }
    public function getProduct($data)
    {
        $productRep = new  ProductRepository();
        $product = array(
            "company_id" => $data["company_id"],
        );
        
        if (isset($data["product_id"])) {
            $product["id"] = $data["product_id"];
        }
        $result = $productRep->getProduct($product);
        echo json_encode($result);  
    }
}