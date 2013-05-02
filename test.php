<?php
require_once 'db.php';

DB::connect([
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'aum'
]);

DB::debug(); // Enable debug mode

// $users = DB::table('users')->get(); // Get all records from users table.

$user = DB::table('users')->first();


var_dump($user);