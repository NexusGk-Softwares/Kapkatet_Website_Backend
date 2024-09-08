<?php
namespace Models;

use PDO;
use PDOException;

class Order {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Place a new order.
     *
     * @param int $userId
     * @param float $totalAmount
     * @param string $paymentStatus
     * @return int|false - Returns the order ID if successful
     */
    public function placeOrder($userId, $totalAmount, $paymentStatus = 'Pending') {
        try {
            $this->pdo->beginTransaction();

            // Insert into orders table
            $stmt = $this->pdo->prepare('INSERT INTO orders (user_id, total_amount, payment_status, order_date) VALUES (:user_id, :total_amount, :payment_status, NOW())');
            $stmt->execute([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'payment_status' => $paymentStatus
            ]);
            $orderId = $this->pdo->lastInsertId();

            // Fetch cart items
            $cartStmt = $this->pdo->prepare('SELECT product_id, quantity, price FROM cart WHERE user_id = :user_id');
            $cartStmt->execute(['user_id' => $userId]);
            $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

            // Insert into order_item table
            $orderItemStmt = $this->pdo->prepare('INSERT INTO order_item (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)');
            foreach ($cartItems as $item) {
                $orderItemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Get order details by ID.
     *
     * @param int $orderId
     * @return array|false
     */
    public function getOrderById($orderId) {
        try {
            $stmt = $this->pdo->prepare('
                SELECT orders.id, orders.user_id, orders.total_amount, orders.payment_status, orders.order_date,
                       users.name, users.email
                FROM orders
                JOIN users ON orders.user_id = users.id
                WHERE orders.id = :order_id
            ');
            $stmt->execute(['order_id' => $orderId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Get all orders for a user.
     *
     * @param int $userId
     * @return array|false
     */
    public function getOrdersByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC');
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Update payment status of an order.
     *
     * @param int $orderId
     * @param string $paymentStatus
     * @return bool
     */
    public function updatePaymentStatus($orderId, $paymentStatus) {
        try {
            $stmt = $this->pdo->prepare('UPDATE orders SET payment_status = :payment_status WHERE id = :order_id');
            return $stmt->execute([
                'payment_status' => $paymentStatus,
                'order_id' => $orderId
            ]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }
}
?>
