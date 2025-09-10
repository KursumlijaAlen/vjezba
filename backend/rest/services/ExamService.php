<?php
require_once __DIR__ . "/../dao/ExamDao.php";

class ExamService
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new ExamDao();
    }

    public function login($data) {
        return $this->dao->login($data);
    }

    public function film_performance_report() {
        return $this->dao->film_performance_report();
    }

    public function test(){
        return "test works in service";
    }

    public function delete_film($film_id) {
        return $this->dao->delete_film($film_id);
    }

    public function edit_film($film_id, $data) {
        return $this->dao->edit_film($film_id, $data);
    }

    public function get_customers_report() {
        return $this->dao->get_customers_report();
    }

    public function get_customer_rental_details($customer_id) {
        return $this->dao->get_customer_rental_details($customer_id);
    }
}
