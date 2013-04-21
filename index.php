<?php

require 'db.php';
require 'user.php';

function dd( $data ) {
	echo "<pre>";
	print_r($data);
	die("</pre>");
}

DB::connect([
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'phpcodemonkey'
]);

// $user = DB::table('users')->find(1);
// var_dump( $user );

// $user->age = 24;
// $user->updated_at = time();


// var_dump( $user );

// $user->updated_at = time();

// var_dump( $user );

// $user->save();


// var_dump( DB::table('users')->get() );

$user = DB::table('users')->where(array('username' => 'jonnothebonno'))->grab(1)->get();
var_dump( $user );

// var_dump(
// DB::table('users')->insert(array(
//   'firstname' => 'John',
//   'lastname' => 'Crossley',
//   'username' => 'jonnothebonno',
//   'password' => sha1('password'),
//   'salt' => md5( time().mt_rand(1, 9) ),
//   'email' => 'hello@phpcodemonkey.com',
//   'gravatar_hash' => md5('hello@phpcodemonkey.com'),
//   'created_at' => time(),
//   'updated_at' => time()
// ))
// );

// var_dump( DB::table('users')->get() );


// $data = DB::table('users')->where(array(
// 	'username'=>'james.mcavady'
// ))->update(array(
// 	'username' => 'jamesponcemcavady'
// ));

// $data = DB::table('users')->where(['username' => 'jamesponcemcavady'])->delete();

// $data = DB::table('users')->get();
