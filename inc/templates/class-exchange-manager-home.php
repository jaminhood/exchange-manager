<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerHome class is created
if (!class_exists('ExchangeManagerHome')) {
  # === Create ExchangeManagerHome class
  class ExchangeManagerHome implements SingletonInterface, PageInterface
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
    }
    public function pageComponent(): void
    { ?>
      <main class="exchange-manager-wrapper">
        <?php
        $this->headerComponent();
        $this->bodyComponent();
        ?>
      </main>
    <?php }
    public function headerComponent(): void
    {
      $logo = EMURL . "assets/imgs/logo-edited.png" ?>
      <section class="exchange-manager-wrapper-header">
        <div class="container mt-5 mb-0">
          <div class="flex-start">
            <img src="<?php echo $logo ?>" alt="LuxTrade" class="admin-logo">
            <h3 class="text-bold">Welcome to Luxtrade Manager.</h3>
          </div>
        </div>
      </section>
    <?php
    }
    public function bodyComponent(): void
    {
      $nullMsg = '<div class="badge badge-danger badge-pill badge-shadow">not set</div>';
      $customers = ExchangeManagerView::getInstance()->getBankDetails();
      $assets = ExchangeManagerView::getInstance()->getTopAssets();
      $allNews = ExchangeManagerView::getInstance()->getTopNews(); ?>
      <section class="exchange-manager-wrapper-header">
        <div class="container pt-3">
          <div class="row">
            <div class="col-12 col-md-8">
              <div class="row">
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <h4 class="text-dark mb-0 text-bold">Bank Details</h4>
                          <span class="font-weight-bold text-muted text-sm mb-0"><?php echo count($customers) ?> customers present.</span>
                        </div>
                        <div class="col-auto">
                          <a href="admin.php?page=em-bank-details" class="btn btn-primary l-bg-green rounded-circle">
                            <i class="fas fa-arrow-right"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <h4 class="text-dark mb-0 text-bold">Top Assets</h4>
                          <span class="font-weight-bold text-muted text-sm mb-0"><?php echo count($assets) ?> top assets present.</span>
                        </div>
                        <div class="col-auto">
                          <a href="admin.php?page=em-top-assets" class="btn btn-primary l-bg-green rounded-circle">
                            <i class="fas fa-arrow-right"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="offset-lg-3 col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <h4 class="text-dark mb-0 text-bold">Top News</h4>
                          <span class="font-weight-bold text-muted text-sm mb-0"><?php echo count($allNews) ?> top news present.</span>
                        </div>
                        <div class="col-auto">
                          <a href="admin.php?page=em-top-news" class="btn btn-primary l-bg-green rounded-circle">
                            <i class="fas fa-arrow-right"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="card text-dark">
                <div class="card-header">
                  <h5 class="text-bold">Top Assets.</h5>
                  <div class="card-header-action">
                    <a data-collapse="#assetsCollapse" class="btn btn-icon btn-primary l-bg-green" href="#">
                      <i class="fas fa-minus"></i>
                    </a>
                  </div>
                </div>
                <div class="collapse show" id="assetsCollapse">
                  <div class="card-body p-0">
                    <?php if (!empty($assets)) :  ?>
                      <div class="table-responsive">
                        <table class="table table-striped table-hover table-md">
                          <thead>
                            <tr>
                              <th>Asset</th>
                              <th class="text-right">Buying Price</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            for ($i = 0; $i < 10; $i++) :
                              $asset = $assets[$i] ?? [];
                              if ($asset === []) {
                                break;
                              } ?>
                              <tr>
                                <td class="text-uppercase text-bold"><?php echo $asset->short_name ?? $nullMsg ?></td>
                                <td class="text-right"><?php echo $asset->buying_price ?? $nullMsg ?></td>
                              </tr>
                            <?php endfor; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-6">
              <div class="card text-dark">
                <div class="card-header">
                  <h5 class="text-bold">Top News.</h5>
                  <div class="card-header-action">
                    <a data-collapse="#newsCollapse" class="btn btn-icon btn-primary l-bg-green" href="#">
                      <i class="fas fa-minus"></i>
                    </a>
                  </div>
                </div>
                <div class="collapse show" id="newsCollapse">
                  <div class="card-body p-0">
                    <?php if (!empty($allNews)) :  ?>
                      <div class="table-responsive">
                        <table class="table table-striped table-md">
                          <thead>
                            <tr>
                              <th>Title</th>
                              <th>Image</th>
                              <th>Date</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            for ($i = 0; $i < 5; $i++) :
                              $news = $allNews[$i] ?? [];
                              if ($news === []) {
                                break;
                              }
                              $newsImg = wp_get_attachment_url($news->newsPicture) ?>
                              <tr>
                                <td>
                                  <strong><?php echo $news->title ?></strong>
                                </td>
                                <td>
                                  <img src="<?php echo $newsImg ?>" alt="<?php echo $news->title ?>" width="100" class="rounded">
                                </td>
                                <td>
                                  <div class="badge badge-pill l-bg-green badge-shadow p-2"><?php echo $news->dateAdded ?></div>
                                </td>
                              </tr>
                            <?php endfor; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="card text-dark">
                <div class="card-header">
                  <h5 class="text-bold">Customers.</h5>
                  <div class="card-header-action">
                    <a data-collapse="#customersCollapse" class="btn btn-icon btn-primary l-bg-green" href="#">
                      <i class="fas fa-minus"></i>
                    </a>
                  </div>
                </div>
                <div class="collapse show" id="customersCollapse">
                  <div class="card-body p-0">
                    <?php if (!empty($customers)) :  ?>
                      <div class="table-responsive">
                        <table class="table table-striped table-md">
                          <thead>
                            <tr>
                              <th>Customer Name</th>
                              <th>Bank Name</th>
                              <th>Account Number</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            for ($i = 0; $i < 5; $i++) :
                              $customer = $customers[$i] ?? [];
                              if ($customer === []) {
                                break;
                              }
                              $fullName = $customer->displayName;
                              $bankName = ucfirst($customer->bankName);
                              $bankAccountNumber = $customer->bankAccountNumber;

                              if ($bankName === '') $bankName = $nullMsg;
                              if ($bankAccountNumber <= 999999999) $bankAccountNumber = $nullMsg; ?>
                              <tr>
                                <td><?php echo $fullName ?></td>
                                <td><?php echo $bankName ?></td>
                                <td><?php echo $bankAccountNumber ?></td>
                              </tr>
                            <?php endfor; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
<?php
    }
  }
}
