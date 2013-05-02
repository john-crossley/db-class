<?php
require_once 'db.php';

DB::connect([
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'aum'
]);

DB::debug(); // Enable debug mode

// $data = DB::table('settings')->get();
$data = DB::table('settings')->find(1);
var_dump($data);

// var_dump( DB::table('settings')->findByUsername('jonnothebonno') );