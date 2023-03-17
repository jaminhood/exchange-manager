<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerUtils class is created
if (!class_exists('ExchangeManagerUtils')) {
  # === Create ExchangeManagerUtils class
  class ExchangeManagerUtils implements SingletonInterface
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
    # === Remove all table template
    public static function removeAllTable()
    {
      # === Global variable required for table
      global $wpdb;
      # === Get all table data in an array
      $tables = [
        'em_top_news',
        'em_customer_bank_details',
      ];
      # === Loop through all table data
      foreach ($tables as $table) {
        # === Get full table name
        $tableName = $wpdb->prefix . $table;
        # === Drop the table if present
        $wpdb->query("DROP TABLE IF EXISTS " . $tableName);
      }
    }
    # === Rewrite Rules for client pages
    public function rewriteRules()
    {
      # === Note that indexing starts from one
      add_rewrite_rule(
        'lux-auth/([a-zA-Z0-9]+)[/]?$', # Regular Expression
        'index.php?authPageName=$matches[1]', # Query Parameters
        'top' # Position on the URL Stack
      );
      add_rewrite_rule(
        'lux-user/([a-zA-Z0-9]+)[/]?$', # Regular Expression
        'index.php?userPageName=$matches[1]', # Query Parameters
        'top' # Position on the URL Stack
      );
    }
    # === Register the query variables for wordpress recognition
    public function registerQueryVariables($query_vars): array
    {
      # === authentication pages
      $query_vars[] = 'authPageName';
      # === user pages
      $query_vars[] = 'userPageName';
      # === Return the Query Variables
      return $query_vars;
    }
    # === Register page templates
    public function registerTemplates($template)
    {
      $authPage = get_query_var('authPageName');
      $userPage = get_query_var('userPageName');
      # === Check if query contains authentication params and if authentication is not empty
      if ($authPage != false && $authPage != '') {
        # === Check if user is logged in
        if (is_user_logged_in()) {
          # === Redirect them to user's dashboard if logged in
          wp_redirect(site_url('/lux-user/dashboard/'));
        } else {
          # === Redirect them to user's authentication page if not logged in
          return EMPATH . 'inc/templates/user/class-exchange-manager-auth-page.php';
        }
      }
      # === Check if query contains user params and if user is not empty
      if ($userPage != false && $userPage != '') {
        # === Check if user is not logged in
        if (!is_user_logged_in()) {
          # === Redirect them to user's auth if not logged in
          wp_redirect(site_url('/lux-auth/login/'));
        } else {
          # === Redirect them to user's dashboard if logged in
          return EMPATH . 'inc/templates/user/class-exchange-manager-dashboard.php';
        }
      }
      # === Return template
      return $template;
    }
    public function guestRates(): void
    {  ?>
      <div class="container-fluid guestRate">
        <div class="row">
          <div class="col-12 col-xs-12 col-lg-12 m-b-30">
            <div class="card card-statistics h-100 mb-0">
              <div class="card-header d-flex justify-content-between">
                <div class="card-heading">
                  <h4 class="card-title">Rates Calculator</h4>
                </div>
              </div>
              <div class="card-body">
                <form method="POST" id="ratesCalculator">
                  <fieldset class="form-group">
                    <div class="row">
                      <div class="col-form-label col-sm-4 pt-3">Asset Type</div>
                      <div class="col-sm-8">
                        <div class="form-check py-2">
                          <input class="form-check-input" type="radio" name="asset_type" id="eCurrency" value="E-Currency">
                          <label class="form-check-label" for="eCurrency">
                            E-currency
                          </label>
                        </div>
                        <div class="form-check py-2">
                          <input class="form-check-input" type="radio" name="asset_type" id="cryptoCurrency" value="Crypto Currency">
                          <label class="form-check-label" for="cryptoCurrency">
                            Crypto-Currency
                          </label>
                        </div>
                        <p class="font-12 py-2 text-muted">What type of asset would you like to purchase?</p>
                      </div>
                    </div>
                  </fieldset>
                  <div class="form-group row select-wrapper">
                    <label for="asset" class="col-sm-4 col-form-label">Select Asset</label>
                    <div class="col-sm-8">
                      <div class="selects-contant">
                        <select class="js-basic-single form-control" name="asset" id="selectAsset">
                          <option value="select">Select Asset</option>
                        </select>
                      </div>
                      <p class="font-12 py-2 text-muted">Select the asset to calculate</p>
                    </div>
                  </div>
                  <div class="form-group row qtyWrapper">
                    <label for="qty" class="col-sm-4 col-form-label">Quantity</label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" id="item-quantity">
                      <p class="font-12 py-2 text-muted">Enter quantity to calculate.</p>
                    </div>
                  </div>
                  <div class="row border-top">
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="buyingPrice" class="col-sm-12 col-form-label">Buying Price</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-buying" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="sellingPrice" class="col-sm-12 col-form-label">Selling Price</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-selling" disabled>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="buyingPerQuantity" class="col-sm-12 col-form-label">Buying Per Quantity</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-buying-q" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row disabled">
                        <label for="sellingPerQuantity" class="col-sm-12 col-form-label">Selling Per Quantity</label>
                        <div class="col-sm-12">
                          <input type="number" class="form-control" id="output-selling-q" disabled>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-12 col-xs-12 col-lg-12 m-b-30">
            <div class="card card-statistics h-100 mb-0">
              <div class="card-header d-flex justify-content-between">
                <div class="card-heading">
                  <h4 class="card-title">E-currency Rates</h4>
                </div>
              </div>
              <div class="card-body p-0">
                <?php
                $eAssets = hid_ex_m_get_all_e_currency_assets();
                if (empty($eAssets)) {
                  echo "<p class='py-3 px-4'>You haven't made any Transactions</p>";
                } else {
                ?>
                  <div class="datatable-wrapper table-responsive">
                    <table id="datatable-user" class="display compact table table-striped table-bordered">
                      <thead>
                        <tr>
                          <td>Icon</td>
                          <td>Name | Allias</td>
                          <td>Buying Price</td>
                          <td>Selling Price</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($eAssets as $asset) { ?>
                          <tr>
                            <td class="p-3" style="width: 3rem;aspect-ratio: 1;overflow: hidden;"><img src="<?php echo wp_get_attachment_url($asset->icon) ?>" style="width: 100%;object-fit: cover;" alt="..."></td>
                            <td><?php echo $asset->name . ' | ' . $asset->short_name ?></td>
                            <td><?php echo $asset->buying_price ?></td>
                            <td><?php echo $asset->selling_price ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="col-12 col-xs-12 col-lg-12 m-b-30">
            <div class="card card-statistics h-100 mb-0">
              <div class="card-header d-flex justify-content-between">
                <div class="card-heading">
                  <h4 class="card-title">Crypto Currency Rates</h4>
                </div>
              </div>
              <div class="card-body p-0">
                <?php
                $cryptoAssets = hid_ex_m_get_all_crypto_currency_assets();
                if (empty($cryptoAssets)) {
                  echo "<p class='py-3 px-4'>You haven't made any Transactions</p>";
                } else {
                ?>
                  <div class="datatable-wrapper table-responsive">
                    <table id="datatable-crypto" class="display compact table table-striped table-bordered">
                      <thead>
                        <tr>
                          <td>Icon</td>
                          <td>Name | Allias</td>
                          <td>Buying Price</td>
                          <td>Selling Price</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($cryptoAssets as $asset) { ?>
                          <tr>
                            <td class="p-3" style="width: 3rem;aspect-ratio: 1;overflow: hidden;"><img src="<?php echo wp_get_attachment_url($asset->icon) ?>" style="width: 100%;object-fit: cover;" alt="..."></td>
                            <td><?php echo $asset->name . ' | ' . $asset->short_name ?></td>
                            <td><?php echo $asset->buying_price ?></td>
                            <td><?php echo $asset->selling_price ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
    }
  }
}
