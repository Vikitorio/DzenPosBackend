<?php

namespace App;
class ProductRepository
{
    private $dbConnection;
    public function __construct(){
        $db = new DBConnection();
        $this->dbConnection = $db->startConnection();
    }
    public function addProduct($product) {
        try {
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

    public function getProduct($product)
    {
        try {
            $stmt = $this->dbConnection->prepare("SELECT * FROM product WHERE company_id = :company_id");
            if(isset($product["id"])){
                $stmt = $this->dbConnnection->prepare("SELECT * FROM product WHERE company_id = :company_id AND id = :id");
            }
            $stmt->execute($product);

                $result = array();
                while ($row = $stmt->fetch()) {
                    $result[] = [
                        "id" => $row["id"],
                        "title" => $row["title"],
                        "type" => $row["type"],
                        "category_id" => $row["category_id"],
                        "description" => $row["description"],
                        "cost" => $row["cost"],
                        "selling_price" => $row["selling_price"],
                        "quantity" => $row["quantity"],
                        "image_src" => $row["image_src"]
                    ];
                }
            

           return $result;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function updateQuantity($product){
        try {
            $con = $this->dbConnection->startConnection();
            $stmt = $con->prepare("UPDATE product SET quantity = quantity + :quantity WHERE id = :productId AND company_id = :companyId");
            $stmt->execute($product);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

}