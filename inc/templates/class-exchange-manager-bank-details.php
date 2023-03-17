<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerBankDetails class is created
if (!class_exists('ExchangeManagerBankDetails')) {
  # === Create ExchangeManagerBankDetails class
  class ExchangeManagerBankDetails implements SingletonInterface, PageInterface
  {
    # === Create static instance of this class
    private static self $instance;
    private int $getId;
    private string $getTab = '';
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
        echo "<script>location.replace('admin.php?page=em-bank-details&success=update&user=" . $_POST['customerName'] . "');</script>";
        return;
      }
    }
    public function pageComponent(): void
    {
      if (isset($_GET['tab'])) {
        $this->getId = $_GET['id'];
        $this->getTab = $_GET['tab'];
      }
      if (isset($_GET['success'])) { ?>
        <script>
          function successMsg(title, msg) {
            iziToast.success({
              title: title,
              message: msg,
              position: "topRight",
            });
          }
          successMsg('Update Successful', '<?php echo $_GET['user'] ?> was updated successfully')
        </script>
      <?php }
      if (isset($_POST['updateForm'])) {
        $this->queryMethod();
      } ?>
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
            <h3 class="text-bold">Bank Details.</h3>
          </div>
        </div>
      </section>
    <?php
    }
    public function bodyComponent(): void
    { ?>
      <section class="exchange-manager-wrapper-body">
        <?php
        if ($this->getTab === 'update') {
          $this->updateComponent($this->getId);
        }

        if ($this->getTab === '') {
          $this->customersComponent();
        }
        ?>
      </section>
    <?php
    }
    public function customersComponent(): void
    {
      $nullMsg = '<div class="badge badge-danger badge-pill badge-shadow">not set</div>';
      $customers = ExchangeManagerView::getInstance()->getBankDetails();
    ?>
      <div class="container pt-1">
        <div class="row">
          <div class="col-12">
            <div class="card text-dark">
              <div class="card-header">
                <h5 class="text-bold">Customer Bank Details Table.</h5>
              </div>
              <div class="card-body p-0">
                <?php if (!empty($customers)) :  ?>
                  <div class="table-responsive pt-5">
                    <table class="table table-striped" id="database-table">
                      <thead>
                        <tr>
                          <th>Fullname</th>
                          <th class="text-center">Bank Name</th>
                          <th class="text-center">Account Name</th>
                          <th class="text-center">Account Number</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($customers as $customer) :
                          $updateUrl = admin_url("admin.php?page=em-bank-details&tab=update&id=$customer->id");

                          $fullName = $customer->displayName;
                          $bankName = ucfirst($customer->bankName);
                          $bankAccountNumber = $customer->bankAccountNumber;
                          $bankAccountName = $customer->bankAccountName;

                          if ($bankName === '') $bankName = $nullMsg;
                          if ($bankAccountNumber <= 999999999) $bankAccountNumber = $nullMsg;
                          if ($bankAccountName === '') $bankAccountName = $nullMsg; ?>
                          <tr>
                            <td><?php echo $fullName ?></td>
                            <td class="text-center"><?php echo $bankName ?></td>
                            <td class="text-center"><?php echo $bankAccountName ?></td>
                            <td class="text-center"><?php echo $bankAccountNumber ?></td>
                            <td>
                              <a href="<?php echo $updateUrl ?>" class="btn btn-primary l-bg-green btn-action mr-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else : ?>
                  <p class="lead text-dark pl-4 pt-2">Sory, No customers to display.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php }
    public function updateComponent(int $id): void
    {
      $customer = ExchangeManagerView::getInstance()->getBankDetail($id);
      foreach ($customer as $data) : ?>
        <form action="<?php echo admin_url("admin.php?page=em-bank-details") ?>" method="POST">
          <div class="card text-dark">
            <div class="card-header">
              <h4><?php echo $data->displayName ?> Bank Details settings</h4>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="bankName">Bank Name</label>
                <input name="id" type="hidden" value="<?php echo $id ?>">
                <input name="customerName" type="hidden" value="<?php echo $data->displayName ?>">
                <input type="text" class="form-control" id="bankName" name="bankName" value="<?php echo $data->bankName ?>" />
              </div>
              <div class="form-group">
                <label for="acctName">Account Name</label>
                <input type="text" class="form-control" id="acctName" name="acctName" value="<?php echo $data->bankAccountName ?>" />
              </div>
              <div class="form-group">
                <label for="acctNumber">Account Number</label>
                <input type="number" class="form-control" id="acctNumber" name="acctNumber" value="<?php echo $data->bankAccountNumber ?>" />
              </div>
            </div>
            <div class="card-footer text-right">
              <button class="btn btn-primary mr-1" name="updateForm" type="submit">
                Update
              </button>
              <button class="btn btn-secondary" type="reset">Reset</button>
            </div>
          </div>
        </form>
<?php
      endforeach;
    }
  }
}
