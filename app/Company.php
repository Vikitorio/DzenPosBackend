<?php

namespace App;
class Company
{
    public function addProduct($userId, $data) {
        $db = new DBConnection();
        try {
            $con = $db->startConnection();

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


    public function getProduct($data)
    {
        $db = new DBConnection();
        try {
            $con = $db->startConnection();
            $query = "SELECT * FROM product WHERE company_id = :company_id";
            $params = [':company_id' => $data["company_id"]];

            if (isset($data["id"])) {
                $query .= " AND id = :id";
                $params[':id'] = $data["id"];
            }

            $stmt = $con->prepare($query);
            $stmt->execute($params);

            if (isset($data["id"])) {
                // Return a single product
                $row = $stmt->fetch();
                if ($row) {
                    $result = [
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
                } else {
                    $result = null;
                }
            } else {
                // Return a product list
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
            }

            echo json_encode($result);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}