## Database Class

I really like Laravels fluent query builder, so I decided to build my own. Its not finished yet but perfectly usable, somethings can be improved but that will be done as I will actively be maintaining it. Feel free to use it =] and feedback would be great!

**Connect to the database**  
First we need to create a connection to the database

	DB::connect([
		'host'	   => 'localhost',
		'username' => 'root',
		'password' => 'password',
		'database' => 'database_name'
	]);
	
**Grab all the records from the users table**  
This will return an array of objects.

	$users = DB::table('users')->get();
	
**Find a record by its ID**  
Assuming you have an ID field called `id` you can grab a record from its id. This will return an object so you can access its properties: `$user->username`
	
	$user = DB::table('users')->find(1);
	
**Insert a record**  
If you would like to insert a record into the users table use this command

	DB::table('users')->insert(array(
		'username' => 'jonnothebonno',
		'password' => sha1('password'),
		'name' 	   => 'John Crossley'
	));
	
**Update a record**  
Update a record using this command

	DB::update('users')->where(array(
		// Where the current username is 'jonnothebonno'
		'username' => 'jonnothebonno'
	))->update(array(
		// Update to be admin
		'username' => 'admin'
	));
	
**Delete a record**  
Delete a record like so

	DB::table('users')
		->where(array('username' => 'jonnothebonno'))
		->delete();

**Delete all records from users**
	
	// Must pass in TRUE otherwise this will fail.
	DB::table('users')->delete(true);

**Order records**  
Order records ASC or DESC
	
	DB::table('users')->order('id', 'DESC')->get();
	
**Limit records pulled**  
Limit records pulled from the database, if 1 is specified this will pull down 1 object to be accessed like `$record->property`. If more than 1 is pulled this will return an array of objects.
	
	DB::table('users')->grab(1)->get();

**RAW query**  
It isn't yet full featured. So for now you can call the raw command to enter your own SQL.
	
	DB::raw('SELECT * FROM users');
	

	
