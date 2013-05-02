<?php
require_once 'db.php';

DB::connect([
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'aum'
]);

// DB::debug(); // Enable debug mode

$data = DB::table('users')->only('email')->findById(1);

var_dump($data);