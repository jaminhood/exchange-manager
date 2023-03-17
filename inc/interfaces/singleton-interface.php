<?php
# === To deny anyone access to this file directly
if (!defined("ABSPATH")) {
  die("Direct access forbidden");
}
# === Checks if SingletonInterface has been created
if (!interface_exists("SingletonInterface")) :
  # === Create SingletonInterface
  interface SingletonInterface
  {
    # === Create a static method used to get instance once
    public static function getInstance(): self;
  }
endif;
