<?php
namespace Models;

use PDO;
use PDOException;

class Product {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Fetch all products.
     *
     * @return array|false
     */
    public function getAllProducts() {
        try {
            $stmt = $this->pdo->query('SELECT * FROM products');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Fetch a product by ID.
     *
     * @param int $id
     * @return array|false
     */
    public function getProductById($id) {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Add a new product.
     *
     * @param array $data
     * @return bool
     */
    public function addProduct($data) {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO products (name, description, price, breed, age, litres_of_milk_per_day, image_url, created_at) VALUES (:name, :description, :price, :breed, :age, :litres_of_milk_per_day, :image_url, NOW())');
            return $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'breed' => $data['breed'],
                'age' => $data['age'],
                'litres_of_milk_per_day' => $data['litres_of_milk_per_day'],
                'image_url' => $data['image_url']
            ]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Update a product.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProduct($id, $data) {
        try {
            $stmt = $this->pdo->prepare('UPDATE products SET name = :name, description = :description, price = :price, breed = :breed, age = :age, litres_of_milk_per_day = :litres_of_milk_per_day, image_url = :image_url WHERE id = :id');
            return $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'breed' => $data['breed'],
                'age' => $data['age'],
                'litres_of_milk_per_day' => $data['litres_of_milk_per_day'],
                'image_url' => $data['image_url'],
                'id' => $id
            ]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return bool
     */
    public function deleteProduct($id) {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }
}
?>
