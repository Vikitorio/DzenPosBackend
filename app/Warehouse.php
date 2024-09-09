<?php
namespace App;
class Warehouse
{

    public function makeWriteOffDocument($data)
    {
        try {
            $waregouseRepo = new WarehouseRepository();
            $lastId = $waregouseRepo->makeWriteOffDocument($data);
            if ($lastId) {
                $waregouseRepo->makeProductWriteOffRecord($lastId, $data["company_id"], $data);
                $result = array();
                $result["status"] = "done";
                echo json_encode($result);
            }

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }

    }

    public function makeWriteOffProducts($documentId, $data)
    {

        $waregouseRepo = new WarehouseRepository();
        $products = $data["write_off"]["products"];
        $companyId = $data["company_id"];
        try {

            foreach ($products as $product) {
                $waregouseRepo->makeProductWriteOffRecord($documentId, $companyId, $product);
                $product = new Product();
                $product->updateQuantity([$product['product_id'], -$product['amount'], $data['company_id']]);
            }

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function makeArrivalDocument($data)
    {
        $warehouseRepo = new WarehouseRepository();
        $documentId = $warehouseRepo->makeArrivalDocument($data);
        if ($documentId !== null) {
            $this->makeWriteOffProducts($documentId, $data);
        }
    }

    public function makeArrivalProducts($documentId, $data)
    {

        $products = $data["arrival"]["products"];
        $sellerId = $data['arrival']['seller_id'];
        $companyId = $data["company_id"];
        $warehouseRepo = new WarehouseRepository();
        try {
            foreach ($products as $product) {
                $warehouseRepo->makeProductArrivalRecord($documentId, $sellerId, $companyId, $product);
                $productInfo = [
                    'product_id' => $product['product_id'],
                    'company_id' => $data['company_id'],
                ];
                $productItem = new Product();
                $currentCost = $productItem->getCurrentCost($productInfo);
                $arrivalCost = $product['cost'];
                $arrivalQuantity = $product['amount'];
                $totalCost = $currentCost;
                $currentQuantity = $productItem->getCurrentQuantity($productInfo);
                if ($currentQuantity > 0) {
                    $totalQuantity = $currentQuantity + $arrivalQuantity;
                    $totalCost = (($currentQuantity * $currentCost) + ($arrivalQuantity * $arrivalCost)) / $totalQuantity;
                } elseif ($currentQuantity <= 0) {
                    $totalCost = $arrivalCost;
                }
                $productItem->updateProductCost([
                    'product_id' => $product['product_id'],
                    'company_id' => $data['company_id'],
                    'cost' => $totalCost,
                ]);
                $productItem->updateProductSellPrice([
                    'product_id' => $product['product_id'],
                    'company_id' => $data['company_id'],
                    'selling_price' => $product['sell_price'],
                ]);
                $productItem->updateQuantity([
                    'product_id' => $product['product_id'],
                    'company_id' => $data['company_id'],
                    'cost' => $totalQuantity,
                ]);
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getWriteOffDocuments($companyId)
    {
        try {
            $warehouseRepo = new WarehouseRepository();
            $result = $warehouseRepo->getWriteOffDocuments($companyId);

            $formattedData = array();
            foreach ($result as $row) {
                $documentId = $row['id'];
                if (!isset($formattedData[$documentId])) {
                    $formattedData[$documentId] = array(
                        'id' => $documentId,
                        'sum' => $row['sum'],
                        'time' => $row['time'],
                        'products' => array()
                    );
                }
                $formattedData[$documentId]['products'][] = array(
                    'id' => $row['product_id'],
                    'amount' => $row['amount'],
                    'cost' => $row['cost'],
                    'document_id' => $row['document_id'],
                    'company_id' => $row['company_id']
                );
            }
            $response = array('data' => array_values($formattedData));
            echo json_encode($response);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
    public function getArrivalDocuments($companyId)
    {
        try {
            $warehouseRepo = new WarehouseRepository();
            $result = $warehouseRepo->getArrivalDocuments($companyId);
            $formattedData = array();
            foreach ($result as $row) {
                $documentId = $row['id'];
                if (!isset($formattedData[$documentId])) {
                    $formattedData[$documentId] = array(
                        'id' => $documentId,
                        'sum' => $row['sum'],
                        'pay_status' => $row['pay_status'],
                        'time' => $row['time'],
                        'products' => array()
                    );
                }
                $formattedData[$documentId]['products'][] = array(
                    'id' => $row['product_id'],
                    'amount' => $row['amount'],
                    'cost' => $row['cost'],
                    'sell_price' => $row['sell_price'],
                    'document_id' => $row['document_id'],
                    'company_id' => $row['company_id']
                );
            }

            $response = array('data' => array_values($formattedData));
            echo json_encode($response);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

}