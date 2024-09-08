<?php
namespace Models;

use PDO;
use PDOException;

class Cart {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Add a product to the user's cart.
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function addToCart($userId, $productId) {
        try {
            // Check if the product is already in the cart
            $stmt = $this->pdo->prepare('SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id');
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cartItem) {
                // If exists, increment the quantity
                $stmt = $this->pdo->prepare('UPDATE cart SET quantity = quantity + 1 WHERE id = :id');
                return $stmt->execute(['id' => $cartItem['id']]);
            } else {
                // If not, insert a new cart item
                $stmt = $this->pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)');
                return $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            }
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Remove a product from the user's cart.
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function removeFromCart($userId, $productId) {
        try {
            // Check if the product exists in the cart
            $stmt = $this->pdo->prepare('SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id');
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cartItem) {
                if ($cartItem['quantity'] > 1) {
                    // Decrement the quantity
                    $stmt = $this->pdo->prepare('UPDATE cart SET quantity = quantity - 1 WHERE id = :id');
                    return $stmt->execute(['id' => $cartItem['id']]);
                } else {
                    // Remove the item from the cart
                    $stmt = $this->pdo->prepare('DELETE FROM cart WHERE id = :id');
                    return $stmt->execute(['id' => $cartItem['id']]);
                }
            }
            return false; // Item not found in cart
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Get all items in the user's cart.
     *
     * @param int $userId
     * @return array|false
     */
    public function getCartItems($userId) {
        try {
            $stmt = $this->pdo->prepare('
                SELECT cart.id, products.id as product_id, products.name, products.price, cart.quantity, (products.price * cart.quantity) as total
                FROM cart
                JOIN products ON cart.product_id = products.id
                WHERE cart.user_id = :user_id
            ');
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Clear the user's cart after order placement.
     *
     * @param int $userId
     * @return bool
     */
    public function clearCart($userId) {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM cart WHERE user_id = :user_id');
            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }
}
?>
