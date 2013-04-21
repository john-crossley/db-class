<?php
/**
 * DB Class
 *
 * A database wrapper class uses PDO. Create fluent and
 * expressive code to create, read, update and delete records.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @version 1.0
 * @package AUM
 */
class DB {

  /**
   * Instance is the current instance
   * of the database class.
   * @var DB
   */
  private static $instance;

  /**
   * DB is an instance of the DB class.
   * @var DB
   */
  private $pdo;

  /**
   * Stores the query string.
   * @var string
   */
  private $query;

  /**
   * queryData - Stores the query data
   * for prepared statements.
   * @var string
   */
  private $queryData;

  private $whereQuery;

  /**
   * limit - Allows the client to add a limit
   * to the query.
   * @var int
   */
  private $limit;

  /**
   * Order - allows the client to order
   * the results fetched from the database.
   * @var string
   */
  private $order;

  private static $host = 'localhost',
                  $username = 'root',
                  $password = 'root',
                  $database = '';

  /**
   * init - Returns an instance of the
   * DB object. Only allows one instance to be created at one time.
   * @return object Returns an instance of the DB object.
   */
  private static function init() {
    if ( !self::$instance instanceof DB ) {
      self::$instance = new DB;
    }
    return self::$instance;
  }

  /**
   * connect - Connects to the database
   * @param  array  $data The connection data
   * @return NULL
   */
  public static function connect(array $data) {

    $expectedKeys = array('username', 'password', 'database', 'host');

    foreach ($data as $key => $value) {
      if ( !in_array($key, $expectedKeys) ) {
        throw new Exception("Invalid option supplied to connect");
      }
    }

    self::$host = $data['host'];
    self::$username = $data['username'];
    self::$password = $data['password'];
    self::$database = $data['database'];
  }

  protected function __construct() {
    try {
      $this->pdo = new PDO('mysql:host='.self::$host.';dbname='.self::$database, self::$username, self::$password, array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
      ));
    } catch (PDOException $e) {
      throw new PDOException("DB_CONN_ERR: {$e->getMessage()}");
    }
  }

  /**
   * Table specifies which table to query.
   * @param  string $table The name of the table.
   * @return object DB object
   */
  public static function table( $table ) {
    $db = static::init();
    $db->table = $table;
    $db->queryData = NULL; // Reset
    return $db;
  }

  /**
   * Raw - Allows the client to pass in RAW SQL queries.
   * @param  string $query      The RAW SQL query to be executed.
   * @return object
   */
  public static function raw($query) {
    $db = static::init();
    $db = $db->pdo; // Break it down love.
    return $db->query($query)->fetchAll();
  }

  /**
   * Grab a particular amount of records from the database.
   * @param  integer $limit The amount of records to retrieve.
   * @return NULL
   */
  public function grab($limit) {
    $this->limit = (int)$limit;
    return $this;
  }

  /**
   * Only - allows the client to specify which columns
   * of data to return. eg: ('email', 'username', 'name')
   * @return object DB
   */
  public function only() {
    $columns = func_get_args(); // Grab the arguments
    if ( empty($columns) )
      $columns[] = '*'; // fetch all instead.
    // Build the query.
    $this->query = 'SELECT ' . implode(', ', $columns) .
      ' FROM ' . $this->table;
    return $this;
  }

  /**
   * Allows the client to specify a where clause.
   * @param  array  $data The data from where to select
   * eg: array( 'username' => 'jonnothebonno' )
   * @return object DB
   */
  public function where( array $data ) {
    $this->query = NULL; // Maybe?
    $this->whereQuery = NULL; // Reset the where query.
    if ( is_array( $data ) ) {
      $tmp = ' WHERE';
      foreach ( $data as $key => $value ) {
        $this->whereQuery .= $tmp . ' ' . $key . ' = ?';
        $tmp = ' AND';
        $this->queryData[] = $value;
      }
    }
    return $this;
  }

  /**
   * Order - allows the client to order the data they receieve.
   * @param  string $column The name of the column.
   * @param  string $order  The order type eg: ASC, DESC
   * @return object DB
   */
  public function order( $column, $order = 'DESC' ) {
    $this->order = $column . ' ' . strtoupper($order);
    return $this;
  }

  /**
   * Perform the get query.
   * @return object Data requested as an object.
   */
  public function get() {
    // Build the query.
    return $this->performQuery();
  }

  /**
   * Find - Finds a record by it's ID
   * Must have ID field for this to work.
   * @param  integer $id The id of the record.
   * @return BOOL
   */
  public function find( $id ) {
    $this->limit = 1; // Set a limit so only one row is fetched.
    $this->where( array('id' => (int)$id) );
    return $this->performQuery();
  }

  /**
   * Insert - Allows the client to insert a record
   * into the database.
   * @param  array  $data The data to be inserted.
   * @return BOOL
   */
  public function insert( array $data ) {
    // Build the first section of the query.
    $this->query = 'INSERT INTO ' . $this->table . ' (';
    $tmp = '';
    foreach ( $data as $key => $value ) {
      $this->query .= $tmp . $key;
      $tmp = ', ';
    }
    // Middle section of the query.
    $this->query .= ') VALUES (';
    $tmp = '';

    // Build the final section of the query.
    foreach ( $data as $key => $value ) {
      $this->query .= $tmp . '?';
      $tmp = ', ';
      $this->queryData[] = $value;
    }
    $this->query .= ');';
    return $this->performQuery('INSERT');
  }

  /**
   * Update - Allows the client to update a record
   * in the database.
   * @param  array  $data The data to be applied.
   * @return BOOL
   */
  public function update( array $data ) {
    $this->query = 'UPDATE ' . $this->table . ' SET ';
    $tmp = '';
    $whereData = $this->queryData; // Store this data for later.
    $this->queryData = array(); // Reset the query data.
    // Build the query.
    foreach ( $data as $key => $value ) {
      $this->query .= $tmp . $key . ' = ?';
      $tmp = ', ';
      $this->queryData[] = $value;
    }
    // Merge the data, cheap and dirt fix for now.
    $this->queryData = array_merge( $this->queryData, $whereData );

    // Peform the query.
    return $this->performQuery('UPDATE');
  }

  /**
   * Delete - Allows the cline to delete a
   * record from the database.
   * @param  boolean $all Setting to true will delete all records
   * if no where clase is found. (Security)
   * @return mixed
   */
  public function delete( $all = FALSE ) {
    if ( empty( $this->whereQuery ) && $all === FALSE ) {
      // Potentially dangerous, user has not
      // provided a where clause.
      die( 'Potentially unsafe action. Really delete everything from the ' . $this->table . '\'s table?' );
    }
    $this->query = 'DELETE FROM ' . $this->table;
    return $this->performQuery('DELETE');
  }

  /**
   * PerformQuery - Peforms the query requested by the client.
   * @param  string $queryType The type of query (INSERT, UPDATE, SELECT, DELETE)
   * @return BOOL
   */
  private function performQuery( $queryType = 'SELECT' ) {

    // Do we have a query?
    if ( empty($this->query) )
      $this->query = 'SELECT * FROM ' . $this->table;

    // Build the query.
    if ( !empty( $this->whereQuery ) )
      $this->query .= $this->whereQuery;

    if ( !empty( $this->order ) )
      $this->query .= ' ORDER BY ' . $this->order;

    if ( !empty( $this->limit ) && $queryType != 'UPDATE' )
      $this->query .= ' LIMIT ' . $this->limit;

    // Prepare the query
    $sth = $this->pdo->prepare( $this->query );



    $sth->execute( $this->queryData );

    // What type of query is this?
    switch (strtoupper($queryType)) {
      case 'SELECT':
        return ( $this->limit == 1 ) ? $sth->fetch() : $sth->fetchAll();
        break;
      case 'INSERT':
      case 'DELETE':
      case 'UPDATE':
        return ( $sth->rowCount() > 0 ) ? true : false;
      break;
      default:
        # code...
        break;
    }
  }

}