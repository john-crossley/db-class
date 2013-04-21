<?php

require 'db.php';

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



$data = DB::table('users')->get();

// $data = DB::table('users')->insert(array(
// 	'firstname' => 'Carl',
// 	'lastname' => 'Evision',
// 	'username' => 'carlos',
// 	'password' => sha1('password'),
// 	'salt' => md5( time().mt_rand(1, 9) ),
// 	'email' => 'newb2ninja@gmail.com',
// 	'gravatar_hash' => md5('newb2ninja@gmail.com'),
// 	'created_at' => time(),
// 	'updated_at' => time()
// ));

// $data = DB::table('users')->where(array(
// 	'username'=>'james.mcavady'
// ))->update(array(
// 	'username' => 'jamesponcemcavady'
// ));

// $data = DB::table('users')->where(['username' => 'jamesponcemcavady'])->delete();

// $data = DB::table('users')->get();

dd( $data );