# Say hi to FastFM!
FastFM is a PHP ORM for Filemaker backends heavily inspired by Rails' Active Model / Active Record combination.

FastFM REQUIRES the official Filemaker PHP API to work.
FastFM is just an extension of the official API, NOT a replacement.
 
FastFM is a lightweight yet powerful ORM that allows for easy management of records as well as PHP model validation.

## Getting Started
FastFM requires little to no setup. All you need to do is include the official filemaker api as well as this PHP file, set up database credentials and your models then you're good to go!

Start by including the libraries required (example):

    // Official Filemaker PHP API
    require_once '/lib/FileMaker.php'   
    // FastFM Filemaker ORM
    require_once '/lib/FastFM.php'

Now you have to set up a connection to the filemaker database through the FilemakerDB singleton class.

    FilemakerDB::setup(host, database, username, password);

Define your model ORM classes and make them extend from FastModel.
Example:

    class User extends FastModel {
        // Every model must have a default layout
        // to read from. 
        public static $layout = 'users';
    	
        // FastModel uses a default unique id of 'id'.
        // but can be overrided by defining the static
        // id variable in the child class.
        public static $id = 'user_id';
    
        public function __construct() {
            $this -> validation = array('name' => Validation::$PRESENCE);
    	}
    }

And that's about it! You should be able to query filemaker through the User class.

    User::find('13') // searches for row in filemaker with user_id = 13

## CRUD
FastFM has 5 functions that can be used for creating, reading, updating and deleting records.
All standard FileMaker operators for newFindCommand can be used.
Any Filemaker Errors that are thrown by the query are thrown as Model Exceptions.

<br />

> **Find (static)**:
Find is used for searching for models through their unique identifier (defined as public static $id on your FastModel).

If no record could be found (Filemaker Error 401), the find command will return null.

    // Searches for user with user_id of 1.
    User::find('1') 
      
    // searches for user with user_id of 2
    // and uses the CompactUser layout to
    // reduce the number of fields returned.
    User::find('2', 'CompactUser')
      
    // Throws ModelException.
    User::find('error')

<br />

> **Where (static)**:
Similar to find, but instead searches based on the array of criteria passed into the function.

    // Returns an array of Users who have "Strong" in their name.
    User::where(array('name' => 'Strong'));
      
    // Returns a User object from the first record that has the email
    // james.strong@etherpros.com
    User::where(array('email' => 'james.strong@etherpros.com'), true);

<br />

> **Create (static)**:
Receives a list of field/value parameters and creates a new FastModel object as well as a database record based on those parameters.
     
    User::create(array('name' => 'James Strong', 'email' => 'james.strong@etherpros.com'));

<br />

> **All (static)**:
Performs a standard FindAllCommand and returns an array of your ORM object.

    User::all();

<br />

> **Update**:
Updates FilemakerDB with changes done to model. Returns true or false.

    $user = User::find('1');
    $user -> email = 'updated@email.com';
    $user -> update();

<br />


> **Update Attributes**:
Same as Update, but receives a list of attributes to update instead of updating dirty fields.

    $user = User::find('1');
    $user -> update_attributes(array('email' => 'updated@email.com'));

<br />

> **Save**:

Performs update() if model points to an existing database entry.
Performs create() if model is a new model.

    Examples:
    (create example)
    $user = new User();
    $user -> name = "James Strong";
    $user -> email = 'james.strong@etherpros.com';
    $user -> save();

## Validation
FastFM has some basic support for validation. Not very extensive at the moment, but still useful.
  
FastFM supports these 3 validation methods out of the box.
    Validation::$PRESENCE
    Validation::$UNIQUENESS
    // email regexp check.
    Validation::$EMAIL_FORMAT
   
In order to use these methods you have to define the validation array in the constructor of your FastFM class.

    class User {
        public function __construct() {
            $this -> validation = array('name' => Validation::$PRESENCE, 
            'email' => array(Validation::$PRESENCE, Validation::$EMAIL_FORMAT), 
            'username' => Validation::$UNIQUENESS); 
       }
    }

The array key is the field you want to validate on and the value is the validation function. In order to have more than one validation for a given field you must pass in a sub-array as a value (see email example).


If you need more validation, you can define your own custom functions to be validated on.
*Validation::$CUSTOM_FUNCTION = 'function on your model'*

The function defined on your model receives one parameter, the validation object on which you add errors per your validation.

    Validation::$CUSTOM_FUNCTION = 'username_no_spaces'
 
    class User {
        // Checks if there are blank spaces inside of username
        public function username_no_spaces($validation) {
            $pos = strpos($value, ' ');
            // if some spaces were found
            if ($pos) {
                $validation -> addError("Username", "Username can't have spaces");
                return false;
           }		      
       }
    }

## Other
FastFM only works with PHP 5.3 (because of its late-static bindings) and has only been tested with FM11, but probably works with previous versions of FM.

Current release is 1.0 Alpha.

If you have any contributions to make, just submit a pull request and I'll take a look,.

## That's it!
That's it for now!

But don't worry, FastFM is under active development and there will be more updates soon!

You can PM me if you are interested in contributing.