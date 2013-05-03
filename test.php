<?php
require_once 'db.php';

DB::connect([
  'host' => 'localhost',
  'username' => 'root',
  'password' => 'root',
  'database' => 'aum'
]);

// DB::debug(); // Enable debug mode

// $data = DB::table('users')->only('email')->findById(1);

// $data = DB::table('users')->only('username')->grab(300)->order('id', 'ASC')->get();

// var_dump($data);

$id = DB::table('users')->insert([
  'username' => 'poppy123',
  'email' => 'hello@poppy.com',
  'password' => sha1('password'),
  'firstname' => 'Poppy',
  'lastname' => 'McGowan'
]);

var_dump($id);