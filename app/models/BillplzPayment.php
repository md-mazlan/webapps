<?php
// app/models/BillplzPayment.php

class BillplzPayment
{
    public $id;
    public $user_id;
    public $collection_id;
    public $paid;
    public $state;
    public $amount;
    public $paid_amount;
    public $due_at;
    public $email;
    public $mobile;
    public $name;
    public $url;
    public $reference_1_label;
    public $reference_1;
    public $reference_2_label;
    public $reference_2;
    public $redirect_url;
    public $callback_url;
    public $description;
    public $paid_at;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}