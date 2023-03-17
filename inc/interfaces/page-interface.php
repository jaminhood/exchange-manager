<?php
# === To deny anyone access to this file directly
if (!defined("ABSPATH")) {
  die("Direct access forbidden");
}
# === Checks if PageInterface has been created
if (!interface_exists("PageInterface")) :
  # === Create PageInterface
  interface PageInterface
  {
    # === Create a query method used for all query around the page
    public function queryMethod(): void;
    # === Create a page component
    public function pageComponent(): void;
    # === Create a header component
    public function headerComponent(): void;
    # === Create a body component
    public function bodyComponent(): void;
  }
endif;
