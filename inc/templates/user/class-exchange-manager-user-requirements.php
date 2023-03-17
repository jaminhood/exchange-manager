<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}

# === Check if ExchangeManagerUserRequirements class is created
if (!class_exists('ExchangeManagerUserRequirements')) {
  # === Create ExchangeManagerUserRequirements class
  class ExchangeManagerUserRequirements implements SingletonInterface
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
    public function mainHeader($user): void
    { ?>
      <!-- begin app-header -->
      <header class="app-header top-bar">
        <!-- begin navbar -->
        <nav class="navbar navbar-expand-md">
          <!-- begin navbar-header -->
          <div class="navbar-header d-flex align-items-center l-bg-green">
            <a href="javascript:void:(0)" class="mobile-toggle"><i class="ti ti-align-right"></i></a>
            <a class="navbar-brand" href="<?php echo site_url('/lux-user/dashboard/') ?>">
              Luxtrade
            </a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="ti ti-align-left"></i>
          </button>
          <!-- end navbar-header -->
          <!-- begin navigation -->
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navigation d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center pl-5">
                <h4 class="nav-user">Hello, <span class="text-primary"><?php echo $user->display_name ?> :)</span></h4>
              </div>
              <div class="pr-5">
                <a class="nav-link" href="<?php echo site_url('/lux-user/logout/') ?>">
                  Logout
                </a>
              </div>
            </div>
          </div>
          <!-- end navigation -->
        </nav>
        <!-- end navbar -->
      </header>
      <!-- end app-header -->
    <?php
    }
    public function sideBar(array $data): void
    { ?>
      <!-- begin app-nabar -->
      <aside class="app-navbar">
        <!-- begin sidebar-nav -->
        <div class="sidebar-nav scrollbar scroll_light">
          <ul class="metismenu" id="sidebarNav">
            <li class="nav-static-title">Personal</li>
            <li <?php if ($data['title'] == 'dashboard' || $data['title'] == 'wallet') echo 'class="active"' ?>>
              <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="nav-icon ti ti-rocket"></i>
                <span class="nav-title">Dashboards</span>
              </a>
              <ul aria-expanded="false">
                <li <?php if ($data['title'] == 'dashboard') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/dashboard/') ?>">Overview</a></li>
                <li <?php if ($data['title'] == 'wallet') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/wallet/') ?>">Wallet</a></li>
              </ul>
            </li>
            <li class="nav-static-title">Market Place</li>
            <li <?php if ($data['title'] == 'buy' || $data['title'] == 'sell') echo 'class="active"' ?>>
              <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="nav-icon ti ti-rocket"></i>
                <span class="nav-title">The Market</span>
              </a>
              <ul aria-expanded="false">
                <li <?php if ($data['title'] == 'buy') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/buy/') ?>">Buy Asset</a></li>
                <li <?php if ($data['title'] == 'sell') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/sell/') ?>">Sell Asset</a></li>
              </ul>
            </li>
            <li class="nav-static-title">Notifications</li>
            <li <?php if ($data['title'] == 'rate' || $data['title'] == 'announcement') echo 'class="active"' ?>>
              <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="nav-icon ti ti-rocket"></i>
                <span class="nav-title">Notifications</span>
              </a>
              <ul aria-expanded="false">
                <li <?php if ($data['title'] == 'rate') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/rate/') ?>">Today's Rate</a></li>
                <li <?php if ($data['title'] == 'announcement') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/announcement/') ?>">Announcement</a></li>
              </ul>
            </li>
            <li class="nav-static-title">General</li>
            <li <?php if ($data['title'] == 'statement' || $data['title'] == 'settings' || $data['title'] == 'support') echo 'class="active"' ?>>
              <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="nav-icon ti ti-rocket"></i>
                <span class="nav-title">General</span>
              </a>
              <ul aria-expanded="false">
                <li <?php if ($data['title'] == 'statement') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/statement/') ?>">Statement</a></li>
                <li <?php if ($data['title'] == 'settings') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/settings/') ?>">Settings</a></li>
                <li <?php if ($data['title'] == 'support') echo 'class="active"' ?>><a href="<?php echo site_url('/lux-user/support/') ?>">Support</a></li>
                <li><a href="<?php echo site_url('/lux-user/logout/') ?>">Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
        <!-- end sidebar-nav -->
      </aside>
      <!-- end app-navbar -->
    <?php
    }
    public function pageHeader(array $data): void
    {
    ?>
      <!DOCTYPE html>
      <html lang="en">

      <head>
        <title><?php echo $data['title'] ?> | Luxtrade</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="Luxtrade customers dashboard" />
        <meta name="author" content="JaminHood" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- === Include wp_header === -->
        <?php wp_head() ?>
      </head>

      <body>
        <!--=== begin app === -->
        <div class="app">
        <?php
      }
      public function pageFooter(): void
      {
        ?>
          <!--=== end app === -->
        </div>
        <!-- === Include wp_footer === -->
        <?php wp_footer() ?>
      </body>

      </html>
<?php
      }
    }
  }
