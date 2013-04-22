<?php

class Crypter {

  public static function prepPassword( $password ) {
    // Generate a random salt.
    $salt = sha1( time().rand() );
    // Make the password
    $password = self::makePassword($password, $salt);
    // Return the data
    return array('password' => $password, 'salt' => $salt);
  }

  public static function makePassword($password, $salt) {
    return sha1( $password.$salt );
  }

}