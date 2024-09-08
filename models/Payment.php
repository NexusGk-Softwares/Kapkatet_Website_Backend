<?php
namespace Models;

use PDO;
use PDOException;

class Payment {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Record a new payment.
     *
     * @param int $orderId
     * @param string $paymentMethod
     * @param string $paymentStatus
     * @return bool
     */
    public function recordPayment($orderId, $paymentMethod, $paymentStatus = 'Pending') {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO payments (order_id, payment_method, payment_status, payment_date) VALUES (:order_id, :payment_method, :payment_status, NOW())');
            return $stmt->execute([
                'order_id' => $orderId,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus
            ]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Get payment details by order ID.
     *
     * @param int $orderId
     * @return array|false
     */
    public function getPaymentByOrderId($orderId) {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE order_id = :order_id');
            $stmt->execute(['order_id' => $orderId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Update payment status.
     *
     * @param int $paymentId
     * @param string $paymentStatus
     * @return bool
     */
    public function updatePaymentStatusById($paymentId, $paymentStatus) {
        try {
            $stmt = $this->pdo->prepare('UPDATE payments SET payment_status = :payment_status WHERE id = :payment_id');
            return $stmt->execute([
                'payment_status' => $paymentStatus,
                'payment_id' => $paymentId
            ]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }
}
?>
