# Database Query Fluent Query Builder

I have built a simple more-or-less full featured database query builder. It works similar to laravels amazing fluent query builder only I decided to build my own ^_^ This is not final but can be used, modified and extended. If you'd like me to add anything else or change something let me know.

## Instructions

Right, so to start off I will be using a database called `user_manager` with a table called `users` which contain the following columns:

  - id
  - firstname
  - lastname
  - username
  - password
  - email

### Connecting

To connect to the database use the following code below. As you can see the **connect** function takes an associative array, containing *host*, *username*, *password* and *database*

    DB::connect(array(
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'database' => 'user_manager'
    ));


### Getting Records

If I want to retrieve all the records from the users table. I can use the following code:

    DB::table('users')->get();

This as is will return an array of objects. If no results are found then an empty array will be returned. You can also specify what gets returned.

	    DB::table('users')->get(array(
	    	'username',
	    	'email',
	    	'password'
	    ));
	    
You can even change their name:

	    DB::table('users')->get(array(
	    	'username AS un',
	    	'email AS e',
	    	'password AS pass'
	    ));

### Find

This method assumes you have an ID field named `id`. To find a record by its ID I can use the following code:

    DB::table('users')->find(1);

This will return the row matching the specified ID. This will return a single object on success and false when nothing is found.


### Find using dynamic methods

I have implemented the use of dynamic methods. Check the following examples to learn how.

    DB::table('users')->findByUsername('jonnothebonno');

    DB::table('users')->findByFirstname('John');

    DB::table('users')->findByEmail('hello@phpcodemonkey.com');
    
    DB::table('users')->findByUsernameAndPassword('username', sha1('password'));

Just follow the pattern **findBySomething**, **findBySomethingAndSomethingElse*** Make sure you pass in the correct number of arguments to the function.

*I'm working on expanding this, so hold on folks. You'll soon be able to use the OR operator*

### JOINING
I have recently implemented the use of SQL joins, not my favourite subject.

	DB::table('user')->join('col1', 'user.id', '=', 1)->get();
	
By default this will produce an INNER join. You can however specify what join you'd like.
	
	DB::table('user')->join('col1', 'user.id', '=', 1, 'LEFT')->get();
	
	DB::table('user')->join('col1', 'user.id', '=', 1, 'OUTER)->get();
	
There is also a left join function:

	DB::table('user')->left_join('col1', 'user.id', '=', 1)->get();
	
You can also specify the data you would like to retrieve like so:

	DB::table('user')->join('col1', 'user.id', '=', 1)->get(array('col1.field2 AS new_field', 'user.username AS uber_name', 'user.password'));
	
You understand right?

### First and Last

To find the first and last records from a table you can use the following code:

    DB::table('users')->first();

    DB::table('users')->last();

*along with*

    DB::table('users')->only('username')->first();
    DB::table('users')->only('username', 'password', 'email')->last();


### Where

To use the where clause you can use the following examples:

    DB::table('users')->where('username', '=', 'jonnothebonno')->get();

You can chain where clauses like so:

    DB::table('users')
             ->where('username', '=', 'jonnothebonno')
             ->where('id', '=', 1)
             ->get();

**Note:** By doing this you will produce an AND chained where clause, if you would like to produce an OR_WHERE simply do:

    DB::table('users')
             ->where('id', '>', 1)
             ->or_where('email', '=', 'hello@phpcodemonkey.com')
             ->get();

### Limit
If you want to limit records pulled from a database append this before you call any methods that will retrieve data.

    DB::table('users')->only('username')->grab(300)->get();

This will return only the usernames of 300 records.

We can apply some order to these results like so:

    DB::table('users')->only('username')->grab(300)->order('id', 'ASC')->get();

These can be applied in any order providing its after the table() and before the get method.



### Inserting

To insert into the database we can use the following methods.

    DB::table('users')->insert([
      'username' => 'poppy123',
      'email' => 'poppy@example.com',
      'password' => sha1('password'),
      'firstname' => 'Poppy',
      'lastname' => 'McGowan'
    ]);

Using the insert function will return true or false depending on success or failure.

To get the ID after inserting a record use the following example:

	$id = DB::table('users')->insert_get_id([
      'username' => 'poppy123',
      'email' => 'poppy@example.com',
      'password' => sha1('password'),
      'firstname' => 'Poppy',
      'lastname' => 'McGowan'
    ]);


### Updating

To update existing data use the following:

    $update = DB::table('users')->where('id', '=', 1)->update(array(
      'username' => 'my.new.username'
    ));

Just update the data by specifying the column names. This will return a BOOL.


### Deleting

To delete a record use the following command:

    DB::table('users')->where('id', '=', 1)->delete();

This will…? Yep you guessed it, delete the user with the ID of 1

… So what if you want to delete all the records from the user table?

    DB::table('users')->delete(true);

**Note:** how we supply true as an argument for delete? This is more of a safety mechanism else the DB class with throw an exception.


### MAX, MIN, AVG and COUNT

Find the maximum, minimum, average and count values of a columns with the following methods.

    DB::table('users')->max();

    DB::table('users')->min();

    DB::table('users')->avg();

    DB::table('users')->count();


### END

*This will be regularly maintained, each time more methods will be added and existing ones will be refined. I will try to keep this as compatible so if you update everything should remain working*

***Just play about with it... If this documentation is not enough please let me know.***