<?php

class User {

  protected $userData;
  protected static $instance;

  public function __construct() {
    $this->newUser = true;
  }

  /**
   * Create a new instance of the user class.
   */
  public static function init() {
    if ( !self::$instance instanceof User )
      self::$instance = new User;
    return self::$instance;
  }

  public function __get( $property ) {
    return (empty($this->userData->$property)) ? NULL : $this->userData->$property;
  }

  public function __set( $property, $value ) {
    if ( !isset($this->userData) )
      $this->userData = new stdClass; // Maybe..?
    $this->userData->$property = $value;
  }

  public function save() {
    $data = array();
    foreach ( $this->userData as $key => $value ) {
      if ( $key == 'id' || $key == 'salt' || $key == 'created_at' )
        continue; // Some fields can't be updated.
      $data[$key] = $value;
    }
    // Has the password been changed?
    if ( $this->userData->originalPassword !== $this->userData->password ) {
      $passwordData = Crypter::prepPassword($data['password']);
      $data['password'] = $passwordData['password'];
      $data['salt'] = $passwordData['salt'];
    }

    $data['updated_at'] = date('Y-m-d H:i:s');

    unset($data['originalPassword']);

    if ( !!$this->newUser === TRUE ) {

      unset($data['newUser']);

      $data['created_at'] = date('Y-m-d H:i:s');

      return DB::table('users')->insert($data);
    }
    return DB::table('users')->where(array('id' => $this->id))->update($data);
  }

  public static function authenticate($username, $password) {
    // Try to fetch the record.
    $user = static::findByUsername($username);

    if ( isset( $user->userData->id ) ) {
      // Alrighty we have a record. Validate it.
      $password = Crypter::makePassword($password, $user->salt);
      $actualPassword = $user->userData->password;
      if ( $password === $actualPassword ) {
        // User has verified correctly
        return "Welcome $username you are now logged in.";
      } else {
        return 'Incorrect username and or password. Try again!';
      }
    } else {
      // Records doesn't exist.
      return 'Unable to retrieve record ' . $username . ' from the database.';
    }
  }

  public static function findByUsername($username) {
    // Create an instance of a new user.
    $user = static::init();
    $result = DB::table('users')
                ->grab(1)
                ->where(array('username' => $username))
                ->get();
    if ( !is_null($result) ) {
      $user->userData = $result;
      $user->userData->originalPassword = $user->userData->password;
    }
    return $user;
  }

  public static function findById($id) {
    $user = static::init();
    $result = DB::table('users')->find($id);
    if ( !empty( $result ) )
      $user->userData = $result;
    return $user;
  }

}




