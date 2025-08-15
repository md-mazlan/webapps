
<?php
// app/controllers/BillplzPaymentController.php

require_once __DIR__ . '/../models/BillplzPayment.php';

class BillplzPaymentController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create or update a Billplz payment record
     */
    public function create(BillplzPayment $payment)
    {
        $sql = "INSERT INTO billplz_payment (
            id, user_id, collection_id, paid, state, amount, paid_amount, due_at, email, mobile, name, url,
            reference_1_label, reference_1, reference_2_label, reference_2, redirect_url, callback_url, description, paid_at
        ) VALUES (
            :id, :user_id, :collection_id, :paid, :state, :amount, :paid_amount, :due_at, :email, :mobile, :name, :url,
            :reference_1_label, :reference_1, :reference_2_label, :reference_2, :redirect_url, :callback_url, :description, :paid_at
        )
        ON DUPLICATE KEY UPDATE
            user_id = VALUES(user_id),
            collection_id = VALUES(collection_id),
            paid = VALUES(paid),
            state = VALUES(state),
            amount = VALUES(amount),
            paid_amount = VALUES(paid_amount),
            due_at = VALUES(due_at),
            email = VALUES(email),
            mobile = VALUES(mobile),
            name = VALUES(name),
            url = VALUES(url),
            reference_1_label = VALUES(reference_1_label),
            reference_1 = VALUES(reference_1),
            reference_2_label = VALUES(reference_2_label),
            reference_2 = VALUES(reference_2),
            redirect_url = VALUES(redirect_url),
            callback_url = VALUES(callback_url),
            description = VALUES(description),
            paid_at = VALUES(paid_at)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $payment->id,
            ':user_id' => $payment->user_id,
            ':collection_id' => $payment->collection_id,
            ':paid' => $payment->paid,
            ':state' => $payment->state,
            ':amount' => $payment->amount,
            ':paid_amount' => $payment->paid_amount,
            ':due_at' => $payment->due_at,
            ':email' => $payment->email,
            ':mobile' => $payment->mobile,
            ':name' => $payment->name,
            ':url' => $payment->url,
            ':reference_1_label' => $payment->reference_1_label,
            ':reference_1' => $payment->reference_1,
            ':reference_2_label' => $payment->reference_2_label,
            ':reference_2' => $payment->reference_2,
            ':redirect_url' => $payment->redirect_url,
            ':callback_url' => $payment->callback_url,
            ':description' => $payment->description,
            ':paid_at' => $payment->paid_at,
        ]);
    }

    /**
     * Get all payments for a specific user
     */
    public function getByUserId($user_id)
    {
        $sql = "SELECT * FROM billplz_payment WHERE user_id = :user_id ORDER BY paid_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new BillplzPayment($row);
        }
        return $results;
    }

    /**
     * Get a payment by its Billplz ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM billplz_payment WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new BillplzPayment($data) : null;
    }

    /**
     * Get all payment records
     */
    public function getAll()
    {
        $sql = "SELECT * FROM billplz_payment ORDER BY paid_at DESC";
        $stmt = $this->db->query($sql);
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new BillplzPayment($row);
        }
        return $results;
    }

    /**
     * Delete a payment record by Billplz ID
     */
    public function delete($id)
    {
        $sql = "DELETE FROM billplz_payment WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
