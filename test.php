<?php
require_once '_db.php';

DB::connect([
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'aum'
]);

DB::debug(); // Enable debug mode