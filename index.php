<?php
require_once 'src/DB.php';

try {

  DB::connect([
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'database' => 'simple_user_manager'
  ]);

  DB::debug();

//  $db = DB::table('user')->order_by('username', 'DESC')->get();
//  $db = DB::table('user')->findByUsername('admin');
//  $db = DB::table('user')->find(1);

//  $db = DB::table('user')->first();

//    $db = DB::table('user')->like('username', 'car')->get();

//     $db = DB::table('user')->where_in('id', array(1, 2))->get();
//    $db = DB::table('user')->where_in('username', array('admin', 'carlospinkz'))->get();


  echo "<pre>";
  print_r($db);


} catch (Exception $e) {
  die($e->getMessage());
}

