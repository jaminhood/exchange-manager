<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Requesting upgrade.php file from wordpress
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
# === Check if ExchangeManagerView class is created
if (!class_exists('ExchangeManagerView')) :
  # === Create ExchangeManagerView class which interacts with the Model class to give data from the database
  class ExchangeManagerView extends ExchangeManagerModel implements SingletonInterface
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
    public function getBankDetails()
    {
      return $this->getCustomersBankDetails();
    }
    public function getBankDetail(int $id)
    {
      return $this->getCustomerBankDetails($id);
    }
    public function getTopAssets()
    {
      return $this->getAllTopAssets();
    }
    public function getTopNews()
    {
      return $this->getAllTopNews();
    }
    public function getOneTopNews(int $id)
    {
      return $this->getSingleTopNews($id);
    }
    public function dashboardData(int $id)
    {
      return $this->getDataForDashboard($id);
    }
    public function walletData(int $id)
    {
      return $this->getDataForWallet($id);
    }
  }
endif;
