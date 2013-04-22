<?php

require 'db.php';
require 'user.php';
require 'crypter.php';

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

// $password = Crypter::prepPassword('password', time().rand());
// var_dump(
//   DB::table('users')->insert(
//     array(
//       'username' => 'admin',
//       'password' => $password['password'],
//       'salt' => $password['salt'],
//       'email' => 'hello@phpcodemonkey.com',
//       'created_at' => date('Y-m-d H:i:s'),
//       'updated_at' => date('Y-m-d H:i:s')
//     )
//   )
// );

// $user = User::authenticate('admin', 'password');

// $user = User::findByUsername('admin');

// $user->firstname = "John";
// $user->lastname = "Crossley";
// $user->password = "password";

// var_dump($user->save());

// var_dump( $user );





