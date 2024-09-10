<?php

namespace App;
class ProductRepository extends \App\Repository
{
    public function addProduct($product)
    {
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
    public function makeProductSale($productSaleData)
    {
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO sales (document_id, product_id, company_id, type, amount, self_price, tradespot, discount, price, time) 
                           VALUES (:document_id, :product_id, :company_id, :type, :amount, :self_price, :tradespot, :discount, :price, NOW())");
            $stmt->execute($productSaleData);
            $productInstance = new Product();
            $productInstance->updateQuantity([
                "product_id" => $productSaleData['product_id'],
                "quantity" => -$productSaleData['amount'],
                "company_id" => $productSaleData["company_id"]
            ]);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getProduct($product)
    {
        try {
            $stmt = $this->dbConnection->prepare("SELECT * FROM product WHERE company_id = :company_id");
            if (isset($product["id"])) {
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
    public function updateQuantity($product)
    {
        try {
            echo $product["quantity"];
            $stmt = $this->dbConnection->prepare("UPDATE product SET quantity = quantity + :quantity WHERE id = :product_id AND company_id = :company_id");
            $stmt->execute($product);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    public function updateProductCost($productId, $newCost, $companyId)
    {
        try {
            $stmt = $this->dbConnection->prepare("UPDATE product SET cost = :cost	WHERE id = :product_id AND company_id = :company_id");
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':cost', $newCost);
            $stmt->bindParam(':company_id', $companyId);
            $stmt->execute();
        } catch (PDOException $e) {
        }
    }
    public function updateProductSellPrice($productId, $newSellPrice, $companyId)
    {
        try {
            $stmt = $this->dbConnection->prepare("UPDATE product SET selling_price = :sell_price	WHERE id = :product_id AND company_id = :company_id");
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':sell_price',  $newSellPrice);
            $stmt->bindParam(':company_id', $companyId);
            $stmt->execute();
        } catch (PDOException $e) {
        }
    }
    public function getCurrentQuantity($data)
    {
        try {
            $stmt = $this->dbConnection->prepare("SELECT quantity FROM product WHERE id = :product_id AND company_id = :company_id");
            $stmt->execute($data);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $currentQuantity = $result['quantity'];
            return $currentQuantity;
        } 
        catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

    }
    public function getCurrentCost($data){
        try {
            $stmt = $this->dbConnection->prepare("SELECT cost FROM product WHERE id = :product_id AND company_id = :company_id");
            $stmt->execute($data);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $currentCost = $result['cost'];
            return $currentCost;
        } 
        catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

}