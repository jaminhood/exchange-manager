<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Requesting upgrade.php file from wordpress
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
# === Check if ExchangeManagerCtrl class is created
if (!class_exists('ExchangeManagerCtrl')) :
  # === Create ExchangeManagerCtrl class which interacts with the Model class to give data from the database
  class ExchangeManagerCtrl extends ExchangeManagerModel implements SingletonInterface
  {
    # === Create static instance of this class
    private static self $instance;
    # === Create a static method used to get instance once
    public static function getInstance(): self
    {
      # === Checks if instance has not been set
      if (!isset(self::$instance)) {
        # === set instance to new object class
        self::$instance = new self;
      }
      # === Return current object class
      return self::$instance;
    }
    public function addNews(array $data)
    {
      $this->setTopNews($data);
    }
    public function removeNews(int $id)
    {
      $this->deleteTopNews($id);
    }
    public function updateBankDetails(array $data, array $where)
    {
      $this->updateCustomerBankDetails($data, $where);
    }
    public function updateSingleNews(array $data, array $where)
    {
      $this->updateTopNews($data, $where);
    }
  }
endif;
