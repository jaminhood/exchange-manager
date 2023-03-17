<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerAdminMenu class is created
if (!class_exists('ExchangeManagerAdminMenu')) {
  # === Create ExchangeManagerAdminMenu class
  class ExchangeManagerAdminMenu implements SingletonInterface
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
    # === Adding menu content
    public function adminMenuContent()
    {
      # === adding plugin in menu
      add_menu_page(
        'Exchange Manager', //page title
        'E Manager', //menu title
        'manage_options', //capabilities
        'em', //menu slug
        [ExchangeManagerHome::getInstance(), 'pageComponent'], //function
        'dashicons-admin-site', // Icon
        11, // Position
      );
      # === adding customer bank details page to plugin menu
      $this->subMenuPage('Bank Details', 'em-bank-details', ExchangeManagerBankDetails::getInstance());
      // # === adding top assets page to plugin menu
      $this->subMenuPage('Top Assets', 'em-top-assets', ExchangeManagerTopAssets::getInstance());
      // # === adding top assets page to plugin menu
      $this->subMenuPage('Top News', 'em-top-news', ExchangeManagerTopNews::getInstance());
    }
    # === Adding aubmenu content
    private function subMenuPage(string $title, string $slug, object $templateCls)
    {
      add_submenu_page(
        'em', //parent page slug
        $title, //page title
        $title, //menu titel
        'manage_options', //manage optios
        $slug, //slug
        [$templateCls, 'pageComponent'] //function
      );
    }
  }
}
