<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerAssets class is created
if (!class_exists('ExchangeManagerAssets')) {
  # === Create ExchangeManagerAssets class
  class ExchangeManagerAssets implements SingletonInterface
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
    # === Adding admin styles
    public function adminStyle()
    {
      # === App style
      wp_register_style('app-style', 'https://jaminhood.github.io/exchange-manager-assets/css/app.min.css', array(), time());
      wp_enqueue_style('app-style');
      # === iziToast style
      wp_register_style('iziToast', 'https://jaminhood.github.io/exchange-manager-assets/css/iziToast.min.css', array(), time());
      wp_enqueue_style('iziToast');
      # === Database table style
      wp_register_style('datatables', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/datatables.min.css', array(), time());
      wp_enqueue_style('datatables');
      # === Database table style
      wp_register_style('datatables-bootstrap', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css', array(), time());
      wp_enqueue_style('datatables-bootstrap');
      # === main style
      wp_register_style('main-style', EMURL . 'assets/css/main.css', array(), time());
      wp_enqueue_style('main-style');
    }
    # === Adding admin scripts
    public function adminScript()
    {
      # === App script
      wp_enqueue_script('app-script', 'https://jaminhood.github.io/exchange-manager-assets/js/app.min.js', array('jquery'), 1, true);
      # === iziToast script
      wp_enqueue_script('iziToast-script', 'https://jaminhood.github.io/exchange-manager-assets/js/iziToast.min.js', array('jquery'), 1, true);
      # === Database script
      wp_enqueue_script('database-script', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/datatables.min.js', array('jquery'), 1, true);
      # === Database bootstrap script
      wp_enqueue_script('database-bootstrap-script', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js', array('jquery'), 1, true);
      # === Main script
      wp_enqueue_script('main-script', EMURL . 'assets/js/main.js', array('jquery'), 1, true);
    }
    # === Adding admin styles
    public function userStyle()
    {
      # === App style
      wp_register_style('app-style', 'https://jaminhood.github.io/exchange-manager-assets/css/app.min.css', array(), time());
      wp_enqueue_style('app-style');
      # === iziToast style
      wp_register_style('iziToast', 'https://jaminhood.github.io/exchange-manager-assets/css/iziToast.min.css', array(), time());
      wp_enqueue_style('iziToast');
      # === Database table style
      wp_register_style('datatables', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/datatables.min.css', array(), time());
      wp_enqueue_style('datatables');
      # === Database table style
      wp_register_style('datatables-bootstrap', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css', array(), time());
      wp_enqueue_style('datatables-bootstrap');
      # === vendors style
      wp_register_style('vendors', 'https://jaminhood.github.io/exchange-manager-assets/css/vendors.css', array(), time());
      wp_enqueue_style('vendors');
      # === main style
      wp_register_style('style', 'https://jaminhood.github.io/exchange-manager-assets/css/style.css', array(), time());
      wp_enqueue_style('style');
      # === reset style
      wp_register_style('user-style', EMURL . 'assets/css/user-style.css', array(), time());
      wp_enqueue_style('user-style');
    }
    # === Adding admin scripts
    public function userScript()
    {
      # === App script
      wp_enqueue_script('app-script', 'https://jaminhood.github.io/exchange-manager-assets/js/app.min.js', array('jquery'), 1, true);
      # === Vendor script
      wp_enqueue_script('vendors-script', 'https://jaminhood.github.io/exchange-manager-assets/js/vendors.js', array('jquery'), 1, true);
      # === iziToast script
      wp_enqueue_script('iziToast-script', 'https://jaminhood.github.io/exchange-manager-assets/js/iziToast.min.js', array('jquery'), 1, true);
      # === Database script
      wp_enqueue_script('database-script', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/datatables.min.js', array('jquery'), 1, true);
      # === Database bootstrap script
      wp_enqueue_script('database-bootstrap-script', 'https://jaminhood.github.io/exchange-manager-assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js', array('jquery'), 1, true);
      # === Main script
      wp_enqueue_script('user-script', EMURL . 'assets/js/user-script.js', array('jquery'), 1, true);
    }
    public function registerClientScript()
    {
      wp_enqueue_script('em-client-js', EMURL . '/assets/js/user-script.js', array('jquery'), time());

      wp_localize_script(
        'em-client-js',
        'scriptData',
        [
          'ajaxurl' => admin_url(
            'admin-ajax.php'
          ),
          'signUpURL' => site_url('/lux-auth/register/'),
          'signInURL' => site_url('/lux-auth/login/'),
          'dashboardURL' => site_url('/lux-user/dashboard/'),
          'security'      => wp_create_nonce('file_upload'),
        ]
      );
    }
  }
}
