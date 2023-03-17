<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerTopNews class is created
if (!class_exists('ExchangeManagerTopNews')) {
  # === Create ExchangeManagerTopNews class
  class ExchangeManagerTopNews implements SingletonInterface, PageInterface
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
      if (isset($_POST['addNewsForm'])) {
        $details = [
          'title' => $_POST['newsTitle'],
          'newsPicture' => $_POST['nwesImageId']
        ];
        ExchangeManagerCtrl::getInstance()->addNews($details);
        echo "<script>location.replace('admin.php?page=em-top-news');</script>";
        return;
      }
      if (isset($_POST['updateNewsForm'])) {
        $details = [
          'title' => $_POST['newsTitle'],
          'newsPicture' => $_POST['nwesImageId'],
          'dateAdded' => date("Y-m-d H:i:s")
        ];

        $where = ['id' => $_POST['newsId']];
        ExchangeManagerCtrl::getInstance()->updateSingleNews($details, $where);
        echo "<script>location.replace('admin.php?page=em-top-news');</script>";
        return;
      }
      if ($this->getTab === 'delete') {
        ExchangeManagerCtrl::getInstance()->removeNews($this->getId);
        echo "<script>location.replace('admin.php?page=em-top-news');</script>";
        return;
      }
    }
    public function pageComponent(): void
    {
      if (isset($_GET['tab'])) {
        $this->getTab = $_GET['tab'];
        if (isset($_GET['id'])) {
          $this->getId = $_GET['id'];
        }
      }
      if (isset($_POST)) {
        $this->queryMethod();
      }
      if ($this->getTab === 'delete') {
        $this->queryMethod();
      } ?>
      <main class="exchange-manager-wrapper">
        <?php $this->headerComponent() ?>
        <?php $this->bodyComponent() ?>
      </main>
    <?php }
    public function headerComponent(): void
    {
      $logo = EMURL . "assets/imgs/logo-edited.png";
      $addUrl = admin_url("admin.php?page=em-top-news&tab=add") ?>
      <section class="exchange-manager-wrapper-header">
        <div class="container mt-5 mb-0">
          <div class="row">
            <div class="col-6">
              <div class="flex-start">
                <img src="<?php echo $logo ?>" alt="LuxTrade" class="admin-logo">
                <h3 class="text-bold">Top News.</h3>
              </div>
            </div>
            <div class="col-6 text-right">
              <a href="<?php echo $addUrl ?>" class="btn btn-danger text-bold l-bg-green">Add News</a>
            </div>
          </div>
        </div>
      </section>
    <?php
    }
    public function bodyComponent(): void
    { ?>
      <section class="exchange-manager-wrapper-body">
        <div class="container pt-1">
          <?php
          if ($this->getTab === 'add') {
            $this->addComponent();
          }
          if ($this->getTab === 'update') {
            $this->updateComponent($this->getId);
          }
          if ($this->getTab === '') {
            $this->allNewsComponent();
          }
          ?>
        </div>
      </section>
    <?php }
    private function updateComponent(int $id)
    {
      $news = ExchangeManagerView::getInstance()->getOneTopNews($id) ?>
      <form action="<?php echo admin_url("admin.php?page=em-top-news") ?>" method="POST">
        <div class="card text-dark">
          <div class="card-header">
            <h4>Update <?php echo $news->title ?></h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="newsTitle">Title</label>
              <input type="hidden" name="newsId" value="<?php echo $news->id ?>">
              <input type="text" class="form-control" id="newsTitle" name="newsTitle" value="<?php echo $news->title ?>" />
            </div>
            <div class="section-title">Image</div>
            <div class="custom-file mt-3">
              <input type="file" name="newsImage" id="newsImage" class="custom-file-input">
              <label for="newsImage" id="newsImageLabel" class="custom-file-label"><?php echo wp_get_attachment_url($news->newsPicture) ?></label>
              <input type="hidden" id="nwesImageId" name="nwesImageId" value="<?php echo $news->newsPicture ?>">
            </div>
          </div>
          <div class="card-footer text-right">
            <button class="btn btn-primary l-bg-green mr-1" name="updateNewsForm" type="submit">
              Update News
            </button>
            <button class="btn btn-secondary" type="reset">Reset</button>
          </div>
        </div>
      </form>
    <?php }
    private function addComponent()
    { ?>
      <form action="<?php echo admin_url("admin.php?page=em-top-news") ?>" method="POST">
        <div class="card text-dark">
          <div class="card-header">
            <h4>Add News</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="newsTitle">Title</label>
              <input type="text" class="form-control" id="newsTitle" name="newsTitle" />
            </div>
            <div class="section-title">Image</div>
            <div class="custom-file mt-3">
              <input type="file" name="newsImage" id="newsImage" class="custom-file-input">
              <label for="newsImage" id="newsImageLabel" class="custom-file-label">Choose File</label>
              <input type="hidden" id="nwesImageId" name="nwesImageId">
            </div>
          </div>
          <div class="card-footer text-right">
            <button class="btn btn-primary mr-1" name="addNewsForm" type="submit">
              Add News
            </button>
            <button class="btn btn-secondary" type="reset">Reset</button>
          </div>
        </div>
      </form>
    <?php }
    private function allNewsComponent()
    {
      $allNews = ExchangeManagerView::getInstance()->getTopNews() ?>
      <div class="row">
        <div class="col-12">
          <div class="card text-dark">
            <div class="card-header">
              <h5 class="text-bold">News Table.</h5>
            </div>
            <div class="card-body p-0">
              <?php if (!empty($allNews)) :  ?>
                <div class="table-responsive pt-5">
                  <table class="table table-striped" id="database-table">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Date</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($allNews as $news) :
                        $updateUrl = admin_url("admin.php?page=em-top-news&tab=update&id=$news->id");
                        $deleteUrl = admin_url("admin.php?page=em-top-news&tab=delete&id=$news->id");
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
                          <td>
                            <a href="<?php echo $updateUrl ?>" class="btn l-bg-green btn-action mr-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            <a href="<?php echo $deleteUrl ?>" class="btn btn-danger btn-action mr-1" title="Delete"><i class="fas fa-trash"></i></a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else : ?>
                <p class="lead text-dark pl-4 pt-2">Sorry, No news to display.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
<?php
    }
  }
}
