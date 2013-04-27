<?php
/**
 * DB Class
 *
 * A database wrapper class that uses PDO. Create expressive
 * database queries without the need to write any SQL code.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @version 1.1
 */
class DB
{
  /**
   * CLASS CONSTANTS
   */
  const SELECT = 'SELECT';
  const INSERT = 'INSERT';
  const UPDATE = 'UPDATE';
  const DELETE = 'DELETE';
  const FETCH_ALL = 'ALL_RECORDS';
  const FETCH_SINGLE = 'SINGLE_RECORD';

  /**
   * Stores an instance of the database object
   * @var object
   */
  private static $instance = null;

  /**
   * Stores an instance of the PDO class
   * @var object
   */
  private $connection = null;

  /**
   * The hostname of the database
   * @var string
   */
  private static $host = "localhost";

  /**
   * The username of the database
   * @var string
   */
  private static $username = "root";

  /**
   * The password of the database
   * @var string
   */
  private static $password = "root";

  /**
   * The database name you would like to connect to
   * @var string
   */
  private static $database = "";

  /**
   * The name of the table in question
   * @var string
   */
  private $table = '';

  /**
   * The current query string
   * @var string
   */
  private $query = '';

  /**
   * The limit of rows to be returned
   * @var integer
   */
  private $limit = null;

  /**
   * The query data to be used for prepared statements
   * array('John', 'Doe', 'john.doe@example.com')
   * @var array
   */
  private $queryData = array();
  private $whereQuery = '';
  private $order = '';
  private $schema = null;
  private $debug = false;
  private $messages = null;

  /**
   * Enable debug mode inside the DB class
   * @return NULL
   */
  public static function debug()
  {
    $db = static::init();
    $db->debug = true;
  }

  /**
   * Can't directly instantiate an instance of
   * the database class.
   */
  private function __construct() {
    try {
      $this->connection = new PDO('mysql:host='.self::$host.';dbname='.self::$database, self::$username, self::$password, array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
      ));
    } catch (PDOException $e) {
      throw new PDOException('Database Connection Error: ' . $e->getMessage());
    }
  }

  /**
   * Checks to see if an instance of the DB object exists
   * and returns it.
   * @return object Instance of the DB class
   */
  public static function init()
  {
    if (!self::$instance instanceof DB) {
      self::$instance = new DB;
    }
    return self::$instance;
  }

  /**
   * Configure the connection to the database.
   * @param  array  $data The credentials to connect to the database
   * array('host' => '', 'username' => '', password => '', 'database' => '')
   * @return NULL
   */
  public static function connect(array $data)
  {
    // Ensure we have the correct parameters to connect to the database.
    $expectedParams = array('host', 'username', 'password', 'database');
    foreach ($data as $key => $value) {
      if (!in_array($key, $expectedParams)) {
        throw new Exception("Invalid option supplied to the connect function.");
      }
    }
    // Set the database credentials
    self::$host = $data['host'];
    self::$username = $data['username'];
    self::$password = $data['password'];
    self::$database = $data['database'];
  }

  /**
   * Specify the table in which we are performing
   * queries on.
   * @param  [type] $table [description]
   * @return [type]        [description]
   */
  public static function table($table)
  {
    // todo Maybe check to see if the table is valid?

    // Get an instance of the database.
    $connection = static::init();
    $connection->table = $table;
    // TODO Reset the database query data.
    $connection->query = '';
    $connection->queryData = array();
    $connection->whereQuery = '';
    return $connection;
  }

  /**
   * This function assumes you have an ID field in the table you
   * specified. This will return the ID of the row
   * @param  integer $id The id of the row
   * @return object|bool
   */
  public function find($id)
  {
    // Specify the where
    $this->where('id', '=', (int)$id)->grab(1);
    return $this->performQuery();
  }

  /**
   * Get the minimum value of the specified columns
   * @param  string $column The name of the column
   * @return array|bool
   */
  public function min($column)
  {
    $this->query = "SELECT MIN($column) as min FROM $this->table";
    return $this->performQuery();
  }

  /**
   * Get the maximum value for the specified
   * @param  string $column The name of the column to find maximum.
   * @return integer|false
   */
  public function max($column)
  {
    $this->limit = 1; // Set the limit
    $this->query = "SELECT MAX($column) as max FROM $this->table";
    return $this->performQuery();
  }

  /**
   * Get the average of a column
   * @param  string $column The name of the column to get the average for
   * @return integer|false
   */
  public function avg($column)
  {
    $this->query = "SELECT AVG($column) as average FROM $this->table";
    return $this->performQuery();
  }
/**
 * Get the sum of a column
 * @param  integer $column The name of the column to return the sum
 * @return integer|false
 */
  public function sum($column)
  {
    $this->limit = 1;
    $this->query = "SELECT SUM($column) AS sum FROM $this->table";
    return $this->performQuery();
  }

  /**
   * Get the count of the items in a column
   * @param  string $column The name of the column
   * @return [type]         [description]
   */
  public function count($column = '*')
  {
    $this->query = "SELECT COUNT($column) as count FROM users";
    return $this->performQuery();
  }

  /**
   * The number of rows you would like returned
   * @param  int $limit The limit you would like to set
   * @return object Returns an instance of the object class
   */
  public function grab($limit)
  {
    $this->limit = (int)$limit;
    return $this;
  }

  protected function describe()
  {
    // We only need to return the first column
    if (empty($this->schema)) {
      $this->schema = static::raw('DESCRIBE ' . $this->table, DB::FETCH_SINGLE);
    }
    return $this->schema;
  }

  public function get()
  {
    return $this->performQuery();
  }

  public function first()
  {
    $schema = $this->describe();
    $field = $schema->Field;
    $this->order = " ORDER BY $field LIMIT 1";
    return $this->performQuery();
  }

  public function last()
  {
    $schema = $this->describe();
    $field = $schema->Field;
    $this->order = " ORDER BY $field DESC LIMIT 1";
    return $this->performQuery();
  }

  public function order($column, $orderBy)
  {
    if (empty($this->order)) {
      $this->order = ' ORDER BY ' . $column . ' ' . strtoupper($orderBy);
    } else {
      $this->order .= ', ' . $column . ' ' . strtoupper($orderBy);
    }
    return $this;
  }

  public static function raw($query, $fetch = DB::FETCH_ALL)
  {
    // Get an instance of the database object
    $db = static::init();
    $db = $db->connection;
    // Store the result
    $result = $db->query($query);

    if (!$result)
      throw new Exception("Error querying the database. Please check again!");

    if ($fetch == DB::FETCH_ALL)
      return $result->fetchAll();
    else
      return $result->fetch();
  }

  public function where($field, $condition, $value)
  {
    // Do we have a where clause already? If we do then
    // append an AND if not create one.
    $where = "$field $condition '$value'";
    if (empty($this->whereQuery)) {
      $this->whereQuery = " WHERE " . $where;
    } else {
      $this->whereQuery .= " AND " . $where;
    }
    return $this;
  }

  public function or_where($field, $condition, $value)
  {
    $where = "$field $condition '$value'";
    if (empty($this->whereQuery)) {
      $this->whereQuery = " WHERE " . $where;
    } else {
      $this->whereQuery .= " OR " . $where;
    }
    return $this;
  }

  public function distinct()
  {
    $this->query = "SELECT DISTINCT * FROM $this->table";
    return $this;
  }

  public function only()
  {
    $columns = func_get_args(); // Grab the arguments
    if (empty($columns)) {
      $columns[] = '*';
    }
    $this->query = 'SELECT ' . implode(', ', $columns) .
      ' FROM ' . $this->table;
    return $this;
  }

  private function performQuery($queryType = DB::SELECT)
  {
    // Do we have an empty query?
    if (empty($this->query) === true) {
      // Create a generic select statement
      $this->query = "SELECT * FROM " . $this->table;
    }

    // Build the query
    if (!empty($this->whereQuery)) {
      $this->query .= $this->whereQuery;
    }

    // Any ordering?
    if (!empty($this->order)) {
      $this->query .= $this->order;
    }

    // Any limitations
    if (!empty($this->limit) && $queryType != 'UPDATE')
      $this->query .= ' LIMIT ' . $this->limit;

    if ($this->debug === true) {
      var_dump($this->query);
    }

    // Prepare the query
    $sth = $this->connection->prepare($this->query);

    // Execute the query
    $sth->execute($this->queryData);

    // Which query does the client want performed?
    switch ($queryType) {
      case 'SELECT':
        return ($this->limit == 1) ? $sth->fetch() : $sth->fetchAll();
        break;
      case 'INSERT':
      case 'UPDATE':
      case 'DELETE':
        return true;
        break;
      default:
        return false;
        break;
    }
  }
}
