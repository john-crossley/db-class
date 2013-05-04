# My Super Awesome Query Builder!

I have built a simple more-or-less full featured database query builder. It works similar to laravels amazing fluent query builder only I decided to buid my own ^_^ This is not final but can be used, modified and extended. If you'd like me to add anything else or change something lemme know.

Right so, using this is sooo simple. Continue reading to learn how!

## Instructions

Right, so to start off I will be using a database called `phpcodemonkey` with a table called `users` which contain the following columns:

  - id
  - firstname
  - lastname
  - username
  - password
  - email

Easy right?


### Connect

To connect to the database we simply call the following command:

    DB::connect(array(
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'database' => 'phpcodemonkey'
    ));


### Get

I want to retrieve all the records from the database.

    DB::table('users')->get();

This as is will return an array of objects. If no results are found then an empty array will be returned.


### Find

This method assumes you have an ID field named... `id` This can be changed to check this but I dunno.. I guess it's common sense to have an ID field eh?

    DB::table('users')->find(1);

This will return the row matching the specified ID. This will return a single object on success and false when nothing is found.


### Find using dynamic methods

Don't you just hate it when you can't remember the name of a method? Me too! Thats why I decided to incorporate dynamic methods into this class. So the way this works is simple you can find data using any columns that already exist in the database, lemme show ya.

    DB::table('users')->findByUsername('jonnothebonno');

    DB::table('users')->findByFirstname('John');

    DB::table('users')->findByEmail('hello@phpcodemonkey.com');

You get the idea right? and now how about finding using multiple where clauses?

    DB::table('users')->findByUsernameAndPassword('jonnothebonno', 'password');

*I'm working on expanding this, so hold on folks. So that you can match OR etc.*


### First and Last

Sometimes you just wanna grab the first and last records from a database. Well you can…

    DB::table('users')->first();

    DB::table('users')->last();

*along with*

    DB::table('users')->only('username')->first();
    DB::table('users')->only('username', 'password', 'email')->last();


### Where

We all need to specify wheres from time to time and with and with this wrapper its pie.

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

    $id = DB::table('users')->insert([
      'username' => 'poppy123',
      'email' => 'poppy@example.com',
      'password' => sha1('password'),
      'firstname' => 'Poppy',
      'lastname' => 'McGowan'
    ]);

Using the insert function will return the ID if successful and false on
failure. You only need to supply the column names you would like to
populate.



### Updating

To update existing data use the following:

    $update = DB::table('users')->where('id', '=', 1)->update(array(
      'username' => 'my.new.username'
    ));

Just update the data by specifying the column names. This will return a BOOL.


### Deleting

To delete a record use the following command:

    DB::table('users')->where('id', '=', 1)->delete();
    
This will…? Yepp you guessed it, delete the user with the ID of 1

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

