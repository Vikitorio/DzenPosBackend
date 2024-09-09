<?php
namespace App;
class WarehouseRepository extends \Repository
{
    public function makeArrivalDocument($data)
    {
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO arrival_doc (company_id, seller_id, cost, pay_status, debt, time) 
                               VALUES (:company_id, :seller_id, :cost, :pay_status, :debt, NOW())");
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':seller_id', $data["arrival"]["seller_id"]);
            $stmt->bindParam(':cost', $data["arrival"]["cost"]);
            $stmt->bindParam(':pay_status', $data["arrival"]["pay_status"]);
            $stmt->bindParam(':debt', $data["arrival"]["debt"]);
            $stmt->execute();
            $lastId = $this->dbConnection->lastInsertId();
            return $lastId;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }


    }
    public function makeProductArrivalRecord($documentId, $companyId, $sellerId, $product)
    {
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO product_arrival (document_id, company_id, seller_id, product_id, category_id, amount, cost, sell_price, time) 
                               VALUES (:document_id, :company_id, :seller_id, :product_id, :category_id, :amount, :cost, :sell_price, NOW())");
            $stmt->bindParam(':document_id', $documentId);
            $stmt->bindParam(':company_id', $companyId);
            $stmt->bindParam(':seller_id', $sellerId);
            $stmt->bindParam(':product_id', $product['product_id']);
            $stmt->bindParam(':category_id', $product['category_id']);
            $stmt->bindParam(':amount', $product['amount']);
            $stmt->bindParam(':cost', $product['cost']);
            $stmt->bindParam(':sell_price', $product['sell_price']);
            $stmt->execute();
        }
    }
    public function makeProductWriteOffRecord($documentId, $companyId, $product)
    {
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO write_off_products (document_id, company_id, product_id, amount, cost) 
                           VALUES (:document_id, :company_id, :product_id, :amount, :cost)");
            $stmt->bindParam(':document_id', $documentId);
            $stmt->bindParam(':company_id', $companyId);
            $stmt->bindParam(':product_id', $product['product_id']);
            $stmt->bindParam(':amount', $product['amount']);
            $stmt->bindParam(':cost', $product['cost']);
            $stmt->execute();
            $product = new Product();
            $product->updateQuantity([$product['product_id'], -$product['amount'], $companyId]);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function makeWriteOffDocument($data)
    {
        try {
            $stmt = $this->dbConnection->prepare("INSERT INTO write_off_doc (company_id, sum) 
                           VALUES (:company_id, :sum)");
            $stmt->bindParam(':company_id', $data["company_id"]);
            $stmt->bindParam(':sum', $data["write_off"]["sum"]);
            $stmt->execute();
            $lastId = $this->dbConnection->lastInsertId();
            
           return $lastId;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getWriteOffDocuments($companyId)
    {
        try {
            $stmt = $this->dbConnection->prepare("SELECT wd.id, wd.sum, wd.time,
                                wp.id AS product_id, wp.amount, wp.cost, wp.document_id, wp.company_id
                                FROM write_off_doc wd
                                JOIN write_off_products wp ON wd.id = wp.document_id
                                WHERE wd.company_id = :company_id");
            $stmt->bindParam(':company_id', $companyId);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getArrivalDocuments($companyId)
    {
        try {
            $stmt = $this->dbConnection->prepare("SELECT ad.id, ad.cost AS sum, ad.pay_status , ad.time, 
                                pa.id AS product_id, pa.amount, pa.cost, pa.sell_price, pa.document_id, pa.company_id
                                FROM arrival_doc ad
                                JOIN product_arrival pa ON ad.id = pa.document_id
                                WHERE ad.company_id = :company_id");
            $stmt->bindParam(':company_id', $companyId);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }


}
?>