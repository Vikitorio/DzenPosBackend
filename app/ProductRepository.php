<?php

namespace App;
class ProductRepository
{
    private $dbConnection;
    public function __construct(){
        $db = new DBConnection();
        $this->dbConnection = $db->startConnection();
    }
    public function updateQuantity($productId, $quantity, $companyId){
        $db = new DBConnection();
        try {
            $stmt = $this->dbConnnection->prepare("UPDATE product SET quantity = quantity + :quantity WHERE id = :productId AND company_id = :companyId");
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':companyId', $companyId);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    public function addProduct($product) {
        try {
            echo implode(",", $product);
            $stmt = $this->dbConnection->prepare("INSERT INTO product (company_id, title, type, category_id, tax_id, description, cost, selling_price, quantity, image_src) VALUE (:company_id, :title, :type, :category_id, :tax_id, :description, :cost, :selling_price, :quantity, :image_src)");
            $stmt->execute($product);
            return "true";

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

}