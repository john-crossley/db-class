<?php
require_once '_db.php';

DB::connect([
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'aum'
]);

// var_dump(
//   DB::table('users')->where(array('username' => 'jonnothebonno'))->get()
// );

DB::debug();

// var_dump(
//   DB::table('users')->where('username', 'LIKE', '%J%')
//                     ->or_where('username', 'LIKE', '%C%')
//                     ->first()
// );

var_dump(
  DB::table('users')->distinct()->get()
);