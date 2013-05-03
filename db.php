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
  const FETCH_ALL = 'FETCH_ALL';
  const FETCH_SINGLE = 'FETCH_SINGLE';

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
   * Allows the creation of dynamic methods. Still needs some
   * work and far from complete.
   * @param  string $method The name of the method
   * @param  array $params The params for the method
   * @return mixed
   */
  public function __call($method, $params)
  {
    // Room for much improvement works for now.
    if (!preg_match('/^(find)By(\w+)$/', $method, $matches)) {
      throw new Exception("Call to undefined method {$method}");
    }
    // Todo OR??
    $criteriaKeys = explode('And', preg_replace('/([a-z0-9])([A-Z])/', '$1$2', $matches[2]));
    $criteriaKeys = array_map('strtolower', $criteriaKeys);
    $criteriaValues = array_slice($params, 0, count($criteriaKeys));
    $criteria = array_combine($criteriaKeys, $criteriaValues);

    $method = $matches[1];
    return $this->$method($criteria);
  }

  /**
   * Reset all of the sql data, if not done
   * this can cause conflicts when querying.
   * @return NULL
   */
  private function reset()
  {
    $conn = static::init();
    $conn->query = '';
    $conn->queryData = array();
    $conn->whereQuery = '';
    $conn->limit = null;
  }

  /**
   * Specify the table in which we are performing
   * queries on.
   * @param  string $table The name of the table
   * @return An instance of the db class
   */
  public static function table($table)
  {
    // TODO Check to see if the table is valid?
    $conn = static::init();
    $conn->table = $table;
    $conn->reset();
    return $conn;
  }

  /**
   * This function assumes you have an ID field in the table you
   * specified. This will return the ID of the row
   * @param  integer $id The id of the row
   * @return object|bool
   */
  public function find($data)
  {

    // What have we been passed?
    if (is_array($data)) {
      // We have been passed an array

      foreach ($data as $key => $value) {
        $this->where($key, '=', $value);
      }

    } else if (is_int($data)) {
      // Assume this is an ID - Could change?
      $this->where('id', '=', (int)$data)->grab(1);
    }

    return $this->execute();
  }

  /**
   * Get the minimum value of the specified columns
   * @param  string $column The name of the column
   * @return array|bool
   */
  public function min($column)
  {
    $this->limit = 1;
    $this->query = "SELECT MIN($column) as min FROM $this->table";
    return $this->execute();
  }

  /**
   * Get the maximum value for the specified
   * @param  string $column The name of the column to find maximum.
   * @return integer|false
   */
  public function max($column)
  {
    $this->limit = 1;
    $this->query = "SELECT MAX($column) as max FROM $this->table";
    return $this->execute();
  }

  /**
   * Get the average of a column
   * @param  string $column The name of the column to get the average for
   * @return integer|false
   */
  public function avg($column)
  {
    $this->limit = 1;
    $this->query = "SELECT AVG($column) as average FROM $this->table";
    return $this->execute();
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
    return $this->execute();
  }

  /**
   * Get the count of the items in a column
   * @param  string $column The name of the column
   * @return [type]         [description]
   */
  public function count($column = '*')
  {
    $this->limit = 1;
    $this->query = "SELECT COUNT($column) as count FROM $this->table";
    return $this->execute();
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

  /**
   * Pulls the information of a table schema, so the the class can
   * know what it needs to know... mwahaha -.- (Shifty Eyes)
   * @return object|bool
   */
  protected function getPrimaryKey()
  {
    $query = $this->query; // Backup query.

    $this->query = 'SHOW KEYS FROM ' . $this->table . ' WHERE Key_name = \'PRIMARY\'';
    $result = $this->execute(DB::SELECT, DB::FETCH_ALL);

    if (empty($result)) return false; // Nothing found prevent errors.

    $primaryKey = $result[0]->Column_name;

    // Ok replace query
    $this->query = $query;

    return $primaryKey;
  }

  /**
   * Perform the query and get the results.
   * @return object|bool
   */
  public function get()
  {
    return $this->execute();
  }

  /**
   * Get the first record of a column
   * @return object|bool
   */
  public function first()
  {
    $primaryKey = $this->getPrimaryKey(); // Should be clear.
    $this->order = ' ORDER BY ' . $primaryKey . ' LIMIT 1';
    return $this->execute();
  }

  /**
   * Get the last record of a column
   * @return object|boolean
   */
  public function last()
  {
    $primaryKey = $this->getPrimaryKey();
    $this->order = ' ORDER BY ' . $primaryKey . ' DESC LIMIT 1';
    return $this->execute();
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

  private function distinct()
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
    $this->query = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $this->table;

    return $this;
  }

  /**
   * Insert a record into the database
   * @param  array  $data The data to be inserted formatted as assoc array
   * @return integer|bool The last insert ID and false on failure
   */
  public function insert(array $data)
  {
    // Build the first section of the query
    $this->query = 'INSERT INTO ' . $this->table . ' (';
    $tmp = '';

    foreach ($data as $key => $value) {
      $this->query .= $tmp . $key;
      $tmp = ', ';
    }

    // Middle section of the query
    $this->query .= ') VALUES (';
    $tmp = '';

    // Build the final section of the query
    foreach ($data as $key => $value) {
      $this->query .= $tmp . '?';
      $tmp = ', ';
      $this->queryData[] = $value;
    }
    $this->query .= ');';
    // Send the query off for processing
    return $this->execute(DB::INSERT);
  }

  /**
   * Update a record in the database, ensure you supply a where clause
   * @param  array  $data The data used to update the record
   * @return bool
   */
  public function update(array $data)
  {
    $this->query = 'UPDATE ' . $this->table . ' SET ';
    $tmp = '';
    $whereData = $this->queryData; // Store this data for a rainy day
    $this->queryData = array(); // Reset the query data
    // Start to build the query
    foreach ($data as $key => $value) {
      $this->query .= $tmp . $key . ' = ?';
      $tmp = ', ';
      $this->queryData[] = $value;
    }
    // Merge the data using this cheap and dirty yet very effective method
    // ...
    // SOLD!
    $this->queryData = array_merge($this->queryData, $whereData);
    // Perform the query
    return $this->execute(DB::UPDATE);
  }

  /**
   * To delete a record from the database
   * @param  boolean $all To delete all the records must pass true
   * @return bool
   */
  public function delete($all = false)
  {
    if (empty($this->whereQuery) && $all === false) {
      throw new Exception("WARNING! You are about to delete all records. Confirm!");
    }
    $this->query = 'DELETE FROM ' . $this->table;
    return $this->execute(DB::DELETE);
  }

  /**
   * This method builds the query from the rest of the information supplied
   * by the chaining. This could be done a little better but for now works.
   * @param  const $type    What type of action should be carried out?
   * @param  const $records How many records are to be fetched
   * @return array|bool|object Depends on what data is requested. If the query
   * fails then false will be returned. If the client requests multiple rows
   * then an array of objects will be returned, finally if a user requests one
   * records then an object will be returned.
   */
  private function execute($type = DB::SELECT, $records = DB::FETCH_ALL)
  {
    if (empty($this->query) === true)
      $this->query = 'SELECT * FROM ' . $this->table;

    if (!empty($this->whereQuery))
      $this->query .= $this->whereQuery;

    if (!empty($this->order))
      $this->query .= $this->order;

    if (!empty($this->limit) && $type !== 'UPDATE')
      $this->query .= ' LIMIT ' . $this->limit;

    // DEBUGGING
    if ($this->debug === true) {
      var_dump($this->query);
      var_dump($this->queryData);
      var_dump($this->whereQuery);
    }

    $sth = $this->connection->prepare($this->query);
    $sth->execute($this->queryData);

    $limit = $this->limit;

    $this->reset();

    switch ($type) {
      case 'SELECT':
        return ($limit == 1) ? $sth->fetch() : $sth->fetchAll();
        break;
      case 'INSERT':
        // This could be done a better way but for now... its fine!
        return ($sth->rowCount() > 0) ? $this->connection->lastInsertId('id') : false;
        break;
      case 'UPDATE':
      case 'DELETE':
        return ($sth->rowCount() > 0) ? true : false;
        break;
      default:
        return false;
        break;
    }
  }

}
