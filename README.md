## Database Class

I loved the way laravels db fluent query builder worked, so I decided to knock one
up. This works more or less the same but not as full featured.

You can use it like so:

  DB::table('users')->get();

This would return all of the records in the users table.

  DB::table('users')->find(1);

This would find a record by it's ID.

  DB::table('users')->where( [ 'username' => 'john-crossley' ] )->grab(1)->get();

You can chain the methods like shown above. This would return the record with
the **username** **john-crossley**

Fnally I'll show you one more because I wan't to get some breakfast ;)

To insert data do:

  DB::table('users')->insert([
    'username' => 'jonnothebonno',
    'email' => 'hello@phpcodemonkey.com',
    'name' => 'John Crossley'
  ]);

You get the idea. It's so easy to use have a go :D
