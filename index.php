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

  $db = new stdClass;

//  $db = DB::table('user')->get(array('username', 'password'));

//  $db = DB::table('user')->order_by('username', 'DESC')->get();
//  $db = DB::table('user')->findByUsername('admin');
//  $db = DB::table('user')->find(1);

//  $db = DB::table('user')->first();

//    $db = DB::table('user')->like('username', 'car')->get();

//     $db = DB::table('user')->where_in('id', array(1, 2))->get();
//    $db = DB::table('user')->where_in('username', array('admin', 'carlospinkz'))->get();

//  $db = DB::table('user')->count();


//  $db = DB::table('user')->min();
//  $db = DB::table('user')->max();

  // join($table, $col1, $operator = null, $col2 = null, $type = 'INNER')
//  $db = DB::table('user')->join('setting', 'user.username', '=', 'admin')->get();

//  $db = DB::table('user')->left_join('setting', 'user.username', '=', 'admin')->get();

//    $db = DB::table('user')->grab(10)->get();

    // $db = DB::table('user')->only('username', 'email');

//    $db = DB::table('user')->only('username', 'email');

//  $db = DB::table('user')->insert(array(
//    'username' => 'my.username',
//    'password' => sha1('password'),
//    'email'    => 'this.email@example.com'
//  ));


//  $db = DB::table('user')->insert_get_id(array(
//    'username' => 'my.new.username',
//    'password' => sha1('password123'),
//    'email'    => 'this.email.another@example.com'
//  ));

//  $db = DB::table('user')->where('id', '=', 4)->update(array(
//    'firstname' => 'Funky',
//    'lastname' => 'Pidgin'
//  ));
//
//  $db = DB::table('user')->where('id', '=', 4)->delete();

//    $db = DB::table('user')->delete(true);




  echo "<pre>";
  print_r($db);


} catch (Exception $e) {
  die($e->getMessage());
}

