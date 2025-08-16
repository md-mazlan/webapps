<?php
// app/controllers/VendorController.php
require_once ROOT_PATH . '/app/models/Vendor.php';
require_once ROOT_PATH . '/php/database.php';

class VendorController {
    private $vendorModel;
    public function __construct() {
        $db = (new Database())->connect();
        $this->vendorModel = new Vendor($db);
    }
    public function getAll() {
        return $this->vendorModel->getAll();
    }
    public function getById($id) {
        return $this->vendorModel->getById($id);
    }
    public function create($data) {
        return $this->vendorModel->create($data);
    }
    public function update($id, $data) {
        return $this->vendorModel->update($id, $data);
    }
    public function delete($id) {
        return $this->vendorModel->delete($id);
    }
}
