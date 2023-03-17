<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerTopAssets class is created
if (!class_exists('ExchangeManagerTopAssets')) {
  # === Create ExchangeManagerTopAssets class
  class ExchangeManagerTopAssets implements SingletonInterface, PageInterface
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
    public function queryMethod(): void
    {
      if (isset($_POST['updateForm'])) {
        $details = [
          'bankName' => $_POST['bankName'],
          'bankAccountNumber' => $_POST['acctNumber'],
          'bankAccountName' => $_POST['acctName']
        ];

        $where = ['id' => $_POST['id']];
        ExchangeManagerCtrl::getInstance()->updateBankDetails($details, $where);
        echo "<script>location.replace('admin.php?page=bank-details&success=update&user=" . $_POST['customerName'] . "');</script>";
        return;
      }
    }
    public function pageComponent(): void
    { ?>
      <main class="exchange-manager-wrapper">
        <?php $this->headerComponent() ?>
        <?php $this->bodyComponent() ?>
      </main>
    <?php }
    public function headerComponent(): void
    {
      $logo = EMURL . "assets/imgs/logo-edited.png" ?>
      <section class="exchange-manager-wrapper-header">
        <div class="container mt-5 mb-0">
          <div class="flex-start">
            <img src="<?php echo $logo ?>" alt="LuxTrade" class="admin-logo">
            <h3 class="text-bold">Top Assets.</h3>
          </div>
        </div>
      </section>
    <?php
    }
    public function bodyComponent(): void
    {
      $nullMsg = '<div class="badge badge-danger badge-pill badge-shadow">null</div>';
      $assets = ExchangeManagerView::getInstance()->getTopAssets() ?>
      <section class="exchange-manager-wrapper-body">
        <div class="container pt-1">
          <div class="row">
            <div class="col-12">
              <div class="card text-dark">
                <div class="card-header">
                  <h5 class="text-bold">Assets Table.</h5>
                </div>
                <div class="card-body p-0">
                  <?php if (!empty($assets)) :  ?>
                    <div class="table-responsive pt-5">
                      <table class="table table-striped" id="database-table">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Short Name</th>
                            <th>Icon</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          foreach ($assets as $asset) : ?>
                            <tr>
                              <td><?php echo $asset->name ?? $nullMsg ?></td>
                              <td class="text-uppercase"><?php echo $asset->short_name ?? $nullMsg ?></td>
                              <td>
                                <img src="<?php echo $asset->image_url ?? '' ?>" alt="<?php echo $asset->name ?? $nullMsg ?>" width="50">
                              </td>
                              <td><?php echo $asset->buying_price ?? $nullMsg ?></td>
                              <td><?php echo $asset->selling_price ?? $nullMsg ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else : ?>
                    <p class="lead text-dark pl-4 pt-2">Sory, No assets to display.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
<?php }
  }
}
