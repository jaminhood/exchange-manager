<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Requesting upgrade.php file from wordpress
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
# === Check if ExchangeManagerDBH class is created
if (!class_exists('ExchangeManagerDBH')) :
  # === Create ExchangeManagerDBH class which performs all CRUD operations on wordpress
  abstract class ExchangeManagerDBH
  {
    # === Tables to be created
    private string $topNews = 'em_top_news';
    private string $bankDetails = 'em_customer_bank_details';
    # === Create table template
    private function createTable($sql): void
    {
      # === Global variable required for table
      global $jal_db_version;
      # === execute sql statement
      dbDelta($sql);
      # === hook the db version
      add_option("jal_db_version", $jal_db_version);
    }
    # === Create top news table
    protected function createTopNewsTable(): void
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be created
      $tableName = $wpdb->prefix . $this->topNews;
      $charsetCollate = $wpdb->get_charset_collate();
      # === sql statement for table creation
      $sql = "CREATE TABLE $tableName (
				id INT NOT NULL AUTO_INCREMENT,
				title TEXT NOT NULL,
				newsPicture INT NOT NULL,
				dateAdded DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) $charsetCollate;";
      # === Calling the create table method
      $this->createTable($sql);
    }
    # === Create customer bank details table
    protected function createCustomerBankDetailsTable(): void
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be created
      $tableName = $wpdb->prefix . $this->bankDetails;
      $charsetCollate = $wpdb->get_charset_collate();
      # === sql statement for table creation
      $sql = "CREATE TABLE $tableName (
				id INT NOT NULL AUTO_INCREMENT,
				displayName TEXT NOT NULL,
				bankName TINYTEXT NOT NULL,
				bankAccountName TINYTEXT NOT NULL,
				bankAccountNumber BIGINT(12) NOT NULL,
				PRIMARY KEY (id)
			) $charsetCollate;";
      # === Calling the create table method
      $this->createTable($sql);
    }
    # === Get customer data
    protected function getCustomerDetails(): array
    {
      # === Setting arguments for query
      $args = ['role' => 'customer'];
      # === Query arguments
      $wpUserQuery = new WP_User_Query($args);
      # === Get arguments result
      $customers = $wpUserQuery->get_results();
      # === Return result
      return $customers;
    }
    # === Get all top news details
    protected function getNews()
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->topNews;
      # === Get table result from database
      $result = $wpdb->get_results("SELECT * FROM $tableName");
      # === return table result
      return $result;
    }
    # === Get single bank detail
    protected function getSingleBankDetail(int $id)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->bankDetails;
      # === Get table result from database
      $result = $wpdb->get_results("SELECT * FROM $tableName WHERE id='$id'");
      # === return table result
      return $result;
    }
    # === Get single bank detail
    protected function getSingleNews(int $id)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->topNews;
      # === Get table result from database
      $result = $wpdb->get_results("SELECT * FROM $tableName WHERE id='$id'");
      # === return table result
      return $result[0];
    }
    # === Get all bank details
    protected function getAllBankDetails()
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->bankDetails;
      # === Get table result from database
      $result = $wpdb->get_results("SELECT * FROM $tableName");
      # === return table result
      return $result;
    }
    # === Get top e-currency
    protected function getTopECurrency()
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . 'hid_ex_m_e_currency_assets';
      # === Get table result from database
      $result = $wpdb->get_results("SELECT * FROM $tableName ORDER BY buying_price DESC LIMIT 3");
      # === return table result
      return $result;
    }
    # === Get top crypto-currency
    protected function getTopCryptoCurrency()
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . 'hid_ex_m_crypto_currency_assets';
      # === Get table result from database
      $result = $wpdb->get_results("SELECT * FROM $tableName ORDER BY buying_price DESC LIMIT 3");
      # === return table result
      return $result;
    }
    # === Get top giftcard
    protected function getTopGiftcards()
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . 'hid_ex_m_giftcards';
      # === Get table result from database
      $result = $wpdb->get_results("SELECT * FROM $tableName ORDER BY buying_price DESC LIMIT 3");
      # === return table result
      return $result;
    }
    # === Get top assets
    protected function getAssets()
    {
      # === Set assets array
      $assets = [];
      # === set e-currency[] data
      foreach ($this->getTopECurrency() as $ecurrency) :
        $assets[] = $ecurrency;
      endforeach;
      # === set CryptoCurrency[] data
      foreach ($this->getTopCryptoCurrency() as $crypto) :
        $assets[] = $crypto;
      endforeach;
      # === set giftcards[] data
      foreach ($this->getTopGiftcards() as $giftcard) :
        $assets[] = $giftcard;
      endforeach;

      if (!empty($assets)) :
        foreach ($assets as $asset) :
          $asset->image_url = wp_get_attachment_url($asset->icon);
          unset($asset->associated_local_bank);
          unset($asset->icon);
        endforeach;
      endif;
      return $assets;
    }
    # === Set top news
    protected function setNews(array $news)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->topNews;
      # === Insert data into database
      $wpdb->insert($tableName, $news);
    }
    # === Set bank details
    protected function setBankDetails(array $details)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->bankDetails;
      # === Insert data into database
      $wpdb->insert($tableName, $details);
    }
    # === Update bank details
    protected function updateCustomerDetails(array $details, array $where)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->bankDetails;
      # === Update data in database
      $wpdb->update($tableName, $details, $where);
    }
    # === Update top news
    protected function updateNews(array $details, array $where)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->topNews;
      # === Update data in database
      $wpdb->update($tableName, $details, $where);
    }
    # === Delete news details
    protected function deleteNews(int $id)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->topNews;
      # === delete data from database
      $wpdb->query("DELETE FROM $tableName WHERE id='$id'");
    }
    # === Delete bank details
    protected function deleteCustomerBankDetails(int $bank_id)
    {
      # === Global variables required for table
      global $wpdb;
      # === Get table full name to be queried
      $tableName = $wpdb->prefix . $this->bankDetails;
      # === delete data from database
      $wpdb->query("DELETE FROM $tableName WHERE id='$bank_id'");
    }
    # === Populate customer bank details
    public function populateCustomerBankDetails(): void
    {
      # === Get all details of customers from database
      $customers = $this->getCustomerDetails();
      # === Loop to change variable keys and values
      foreach ($customers as $customer) :
        $id = $customer->ID;
        $customerData = $customer->data;
        $displayName = $customerData->display_name;
        $userNiceName = $customerData->user_nicename;

        $customerInfo = [
          'id' => $id,
          'displayName' => "$displayName $userNiceName"
        ];
        # === Loop to Set bank details of all customers
        for ($i = 0; $i < count($customers); $i++) :
          $this->setBankDetails($customerInfo);
        endfor;
      endforeach;
    }

    protected function getDashboardData($userId)
    {
      global $wpdb;

      $totalSold = 0;
      $sellEcurrency = 0;
      $sellCrypto = 0;
      $sellWithinMonth = 0;
      $totalBought = 0;
      $buyEcurrency = 0;
      $buyCrypto = 0;
      $buyWithinMonth = 0;
      $totalTransactions = 0;
      $pendingPayments = 0;
      $currentBal = 0;

      $tableName = $wpdb->prefix . 'hid_ex_m_buy_orders';
      $resultBuy = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId'");
      $resultBuyWithinMonth = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId' AND time_stamp > NOW() - interval 1 month");
      $resultBuyCrypto = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId' AND asset_type=2");
      $resultBuyEcurrency = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId' AND asset_type=1");

      if (!empty($resultBuy)) :
        foreach ($resultBuy as $order) :
          $totalBought += $order->fee;
          if ($order->order_status == 1) :
            $pendingPayments += $order->fee;
          endif;
        endforeach;
      endif;

      if (!empty($resultBuyWithinMonth)) :
        foreach ($resultBuyWithinMonth as $order) :
          $buyWithinMonth += $order->fee;
        endforeach;
      endif;

      if (!empty($resultBuyEcurrency)) :
        foreach ($resultBuyEcurrency as $order) :
          $buyEcurrency += $order->fee;
        endforeach;
      endif;

      if (!empty($resultBuyCrypto)) :
        foreach ($resultBuyCrypto as $order) :
          $buyCrypto += $order->fee;
        endforeach;
      endif;

      if ($totalBought > 0) {
        $ecurrencyBuyPercent = round(($buyEcurrency / $totalBought) * 100, 2);
        $cryptoBuyPercent = round(($buyCrypto / $totalBought) * 100, 2);
        $buyPercentWithinMonth = round(($buyWithinMonth / $totalBought) * 100, 2);
      } else {
        $ecurrencyBuyPercent = '0.00';
        $cryptoBuyPercent = '0.00';
        $buyPercentWithinMonth = '0.00';
      }

      $tableName = $wpdb->prefix . 'hid_ex_m_sell_orders';
      $resultSell = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId'");
      $resultSell = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId'");
      $resultSellWithinMonth = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId' AND time_stamp > NOW() - interval 1 month");
      $resultSellCrypto = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId' AND asset_type=2");
      $resultSellEcurrency = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId' AND asset_type=1");

      if (!empty($resultSell)) :
        foreach ($resultSell as $order) :
          $totalSold += $order->amount_to_recieve;
          if ($order->order_status == 1) :
            $pendingPayments += $order->amount_to_recieve;
          endif;
        endforeach;
      endif;

      if (!empty($resultSellWithinMonth)) :
        foreach ($resultSellWithinMonth as $order) :
          $sellWithinMonth += $order->amount_to_recieve;
        endforeach;
      endif;

      if (!empty($resultSellEcurrency)) :
        foreach ($resultSellEcurrency as $order) :
          $sellEcurrency += $order->amount_to_recieve;
        endforeach;
      endif;

      if (!empty($resultSellCrypto)) :
        foreach ($resultSellCrypto as $order) :
          $sellCrypto += $order->amount_to_recieve;
        endforeach;
      endif;

      if ($totalSold > 0) {
        $ecurrencySellPercent = round(($sellEcurrency / $totalSold) * 100, 2);
        $cryptoSellPercent = round(($sellCrypto / $totalSold) * 100, 2);
        $sellPercentWithinMonth = round(($buyWithinMonth / $totalSold) * 100, 2);
      } else {
        $ecurrencySellPercent = '0.00';
        $cryptoSellPercent = '0.00';
        $sellPercentWithinMonth = '0.00';
      }

      $totalTransactions = count($resultSell) + count($resultBuy);
      $allOrders = array_merge($resultBuy, $resultSell);

      if (count($allOrders) > 1) usort($allOrders, 'date_compare');

      if (count($allOrders) > 5) $allOrders = array_slice($allOrders, 0, 5);

      $announcements = hid_ex_m_get_all_announcements();

      if (count($announcements) > 3) $announcements = array_slice($announcements, 0, 3);

      if (!metadata_exists('user', $userId, 'account_balance')) {
        add_user_meta($userId, 'account_balance', 0);
      }

      if (!metadata_exists('user', $userId, 'can_withdraw')) {
        add_user_meta($userId, 'can_withdraw', 1);
      }

      $currentBal = round(get_user_meta($userId, 'account_balance')[0], 2);

      $data = [
        'totalBought'           => $totalBought,
        'buyPercentWithinMonth' => $buyPercentWithinMonth,
        'buyEcurrency'          => $buyEcurrency,
        'ecurrencyBuyPercent'   => $ecurrencyBuyPercent,
        'buyCrypto'             => $buyCrypto,
        'cryptoBuyPercent'      => $cryptoBuyPercent,
        'totalSold'             => $totalSold,
        'sellPercentWithinMonth' => $sellPercentWithinMonth,
        'sellEcurrency'          => $sellEcurrency,
        'ecurrencySellPercent'   => $ecurrencySellPercent,
        'sellCrypto'             => $sellCrypto,
        'cryptoSellPercent'      => $cryptoSellPercent,
        'totalTransactions'     => $totalTransactions,
        'pendingPayments'       => $pendingPayments,
        'announcements'         => $announcements,
        'orders'                => $allOrders,
        'walletBalance'         => $currentBal
      ];

      return $data;
    }

    protected function getWalletData($userId)
    {
      global $wpdb;

      $totalTransactions = 0;
      $pendingPayments = 0;

      $tableName = $wpdb->prefix . 'hid_ex_m_wallet_transactions';

      $allTransactions = $wpdb->get_results("SELECT * FROM $tableName WHERE customer_id='$userId' ORDER BY time_stamp DESC");

      if (!empty($allTransactions)) {
        $totalTransactions = count($allTransactions);

        foreach ($allTransactions as $transaction) {
          if ($transaction->transaction_status == 1) {
            $pendingPayments += $transaction->amount;
          }
        }
      }

      $result = array(
        'accountBalance'   => hid_ex_m_get_account_balance($userId),
        'canWithdraw'   => hid_ex_m_get_withdrawal_status($userId),
        'totalTransactions'    => $totalTransactions,
        'pendingPayments'  => $pendingPayments,
        'allTransactions'  => $allTransactions
      );

      return $result;
    }
  }
endif;
