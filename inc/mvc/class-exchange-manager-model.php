<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Requesting upgrade.php file from wordpress
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
# === Check if ExchangeManagerModel class is created
if (!class_exists('ExchangeManagerModel')) :
  # === Create ExchangeManagerModel class which interacts with the DBH class
  abstract class ExchangeManagerModel extends ExchangeManagerDBH
  {
    protected function getCustomersBankDetails()
    {
      return $this->getAllBankDetails();
    }
    protected function getCustomerBankDetails(int $id)
    {
      return $this->getSingleBankDetail($id);
    }
    protected function getAllTopAssets()
    {
      return $this->getAssets();
    }
    protected function getAllTopNews()
    {
      return $this->getNews();
    }
    protected function getSingleTopNews(int $id)
    {
      return $this->getSingleNews($id);
    }
    protected function setTopNews(array $data)
    {
      $this->setNews($data);
    }
    protected function updateTopNews(array $data, array $where)
    {
      $this->updateNews($data, $where);
    }
    protected function updateCustomerBankDetails(array $data, array $where)
    {
      $this->updateCustomerDetails($data, $where);
    }
    protected function deleteTopNews(int $id)
    {
      $this->deleteNews($id);
    }
    protected function getDataForDashboard(int $id)
    {
      return $this->getDashboardData($id);
    }
    protected function getDataForWallet(int $id)
    {
      return $this->getWalletData($id);
    }
  }
endif;
