<?php

/**
 * Plugin Name: Exchange Manager
 * Plugin URI: https://github.com/jaminhood/exchange-manager
 * Version: 1.0.0
 * Description: This plugin will provide backend api support for mobile applications and also create a user dashboard as well on the website
 * Author: JaminHood
 * Author URI: https://github.com/jaminhood
 * License: GPU
 * Text Domain: exchange-manager
 */

# === To deny anyone access to this file directly
if (!defined("ABSPATH")) {
  die("Direct access forbidden");
}
# === Plugin path
if (!defined("EMPATH")) {
  define("EMPATH", plugin_dir_path(__FILE__));
}
# === Plugin url
if (!defined("EMURL")) {
  define("EMURL", plugin_dir_url(__FILE__));
}
# === Requesting files from external scripts
require_once(EMPATH . "inc/interfaces/singleton-interface.php");
require_once(EMPATH . "inc/mvc/class-exchange-manager-dbh.php");
# === Checks if ExchangeManager class has been created
if (!class_exists("ExchangeManager")) :
  # === Create ExchangeManager class which extends ExchangeManagerDBH class and implements the singleton interface
  class ExchangeManager extends ExchangeManagerDBH implements SingletonInterface
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
    # === Constructor function
    private function __construct()
    {
      # === Interfaces
      require_once(EMPATH . "inc/interfaces/page-interface.php");
      # === MVC
      require_once(EMPATH . "inc/mvc/class-exchange-manager-model.php");
      require_once(EMPATH . "inc/mvc/class-exchange-manager-view.php");
      require_once(EMPATH . "inc/mvc/class-exchange-manager-ctrl.php");
      # === Templates
      require_once(EMPATH . "inc/templates/class-exchange-manager-home.php");
      require_once(EMPATH . "inc/templates/class-exchange-manager-bank-details.php");
      require_once(EMPATH . "inc/templates/class-exchange-manager-top-assets.php");
      require_once(EMPATH . "inc/templates/class-exchange-manager-top-news.php");
      require_once(EMPATH . "inc/templates/user/class-exchange-manager-user-requirements.php");
      # === REST
      require_once(EMPATH . "inc/rest/class-exchange-manager-rest.php");
      # === AJAX
      require_once(EMPATH . "inc/ajax/auth-fn.php");
      # === Others
      require_once(EMPATH . "inc/class-exchange-manager-utils.php");
      require_once(EMPATH . "inc/class-exchange-manager-admin-menu.php");
      require_once(EMPATH . "inc/class-exchange-manager-assets.php");
      # === Calling Init method
      $this->init();
    }
    # === Create Init method
    public function init(): void
    {
      # === Registering activation
      register_activation_hook(__FILE__, [$this, "activate"]);
      # === Registering deactivation
      register_deactivation_hook(__FILE__, [$this, "deactivate"]);
      # === Populate the bank details table
      add_action('init', [$this, 'populateCustomerBankDetails']);
      # === Rewrite rules for pages
      add_action('init', [ExchangeManagerUtils::getInstance(), 'rewriteRules']);
      # === Register pages query variables
      add_filter('query_vars', [ExchangeManagerUtils::getInstance(), 'registerQueryVariables']);
      # === Register page templates
      add_action('template_include', [ExchangeManagerUtils::getInstance(), 'registerTemplates']);
      # === Add Shortcode to guests rate page
      add_shortcode('em_guest_rate', [ExchangeManagerUtils::getInstance(), 'guestRates']);
      # === Add the admin menu
      add_action('admin_menu', [ExchangeManagerAdminMenu::getInstance(), 'adminMenuContent']);
      # === Enqueue admin scripts
      add_action('admin_enqueue_scripts', [ExchangeManagerAssets::getInstance(), 'adminStyle']);
      add_action('admin_enqueue_scripts', [ExchangeManagerAssets::getInstance(), 'adminScript']);
      # === Enqueue user scripts
      add_action('wp_enqueue_scripts', [ExchangeManagerAssets::getInstance(), 'userStyle']);
      add_action('wp_enqueue_scripts', [ExchangeManagerAssets::getInstance(), 'userScript']);
      add_action('wp_enqueue_scripts', [ExchangeManagerAssets::getInstance(), 'registerClientScript']);
      # === Rest methods
      ExchangeManagerRest::getInstance()->routes();
    }
    # === Activation method
    public function activate(): void
    {
      # === Populate all tables on activation
      $this->createCustomerBankDetailsTable();
      $this->createTopNewsTable();
    }
    # === Deactivation method
    public function deactivate(): void
    {
      ExchangeManagerUtils::removeAllTable();
    }
  }
  # === Getting the instance of ExchangeManager class
  ExchangeManager::getInstance();
endif;
