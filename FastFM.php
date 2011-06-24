<?php
/**
 * ======
 * FastFM
 * ======
 *   FastFM is a PHP ORM for Filemaker backends heavily inspired
 *   by Rails' Active Model / Active Record combination.
 * 
 *   FastFM REQUIRES the official Filemaker PHP API to work.
 *   FastFM is just an extension of the official API, NOT
 *   a replacement.
 * 
 *   FastFM is a lightweight yet powerful ORM that allows for easy
 *   management of records as well as PHP model validation.
 *   
 *   @author: James Strong
 *   @copyright: None!
 *   @license: Do whatever you want with this. No restrictions whatsoever.
 *   @version: 1.0 Alpha.
 *   
 *   ===============
 *   Getting Started
 *   ===============
 *   FastFM requires little to no setup. All you
 *   need to do is include the official filemaker api
 *   as well as this PHP file, set up database credentials and
 *   your models then you're good to go!
 *   
 *   Start by including the libraries required:
 *   
 *   // Official Filemaker PHP API
 *   require_once '/lib/FileMaker.php'   
 *   // FastFM Filemaker ORM
 *   require_once '/lib/FastFM.php'
 *   
 *   Now you have to set up a connection to the filemaker database
 *   through the FilemakerDB singleton class (part of the framework)
 *   
 *   FilemakerDB::setup(host, database, username, password);
 *   
 *   Define your model ORM classes and make them extend from FastModel.
 *   Example:
 *   class User extends FastModel {
 *   	// Every model must have a default layout
 *   	// to read from. 
 *   	public static $layout = 'users';
 *   	
 *   	// FastModel uses a default unique id of 'id'.
 *   	// but can be overrided by defining the static
 *   	// id variable in the child class.
 *   	public static $id = 'user_id';
 *   
 *   	public function __construct() {
 *   		$this -> validation = array('name' => Validation::$PRESENCE);
 *   	}
 *   }
 *
 *   And that's about it! You should be able to query filemaker through the User class.
 *   User::find('13') // searches for row in filemaker with user_id = 13
 *   
 *   ============
 *   CRUD queries
 *   ============
 *   FastFM has 5 functions that can be used for creating,
 *   reading, updating and deleting records.
 *   
 *   All standard FileMaker operators for newFindCommand
 *   can be used.
 *   
 *   Any Filemaker Errors that are thrown by the query are thrown as
 *   Model Exceptions.
 *   
 *   -- Find (static) --
 *     Find is used for searching for models through
 *     their unique identifier (defined as public static $id on your FastModel).
 *   
 *     If no record could be found (Filemaker Error 401), 
 *     the find command will return null.
 *   
 *   
 *     Examples:
 *     // searches for user with user_id of 1.
 *     User::find('1') 
 *     
 *     // searches for user with user_id of 2
 *     // and uses the CompactUser layout to
 *     // reduce the number of fields returned.
 *     User::find('2', 'CompactUser')
 *     
 *     // Throws ModelException.
 *     User::find('error')
 *     
 *    
 *   -- Where (static) --
 *     Similar to find, but instead searches based on the array of criteria
 *     passed into the function.
 *     
 *     Examples:
 *     // Returns an array of Users who have "Strong" in their name.
 *     User::where(array('name' => 'Strong'));
 *     
 *     // Returns a User object from the first record that has the email
 *     // james.strong@etherpros.com
 *     User::where(array('email' => 'james.strong@etherpros.com'), true);
 *   
 *   
 *     
 *   -- Create (static) --
 *     Receives a list of field/value parameters and creates a new FastModel
 *     object as well as a database record based on those parameters.
 *     
 *     Example:
 *    User::create(array('name' => 'James Strong', 'email' => 'james.strong@etherpros.com'));
 *     
 *   
 *   -- All (static) --
 *     Performs a standard FindAllCommand and returns an array of your ORM object.
 *     
 *     Example:
 *     User::all();
 *     
 *   
 *   -- Update --
 *     Updates FilemakerDB with changes done to model.
 *     Returns true or false.
 *     
 *     Example:
 *     $user = User::find('1');
 *     $user -> email = 'updated@email.com';
 *     $user -> update();
 *         
 *    
 *   -- Update Attributes --
 *     Same as Update, but receives a list of attributes
 *     to update instead of updating dirty fields.
 *     
 *     Example:
 *     $user = User::find('1');
 *     $user -> update_attributes(array('email' => 'updated@email.com'));
 *     
 *     
 *     
 *   -- Save --
 *     Performs update() if model points to an existing database entry.
 *     Performs create() if model is a new model.
 *     
 *     Examples:
 *     (create example)
 *     $user = new User();
 *     $user -> name = "James Strong";
 *     $user -> email = 'james.strong@etherpros.com';
 *     $user -> save();
 *    
 *    
 *  ==========
 *  Validation
 *  ==========
 *  FastFM has some basic support for validation.
 *  Not very extensive at the moment, but still useful.
 *  
 *  FastFM supports these 3 validation methods out of the box.
 *    Validation::$PRESENCE
 *    Validation::$UNIQUENESS
 *    // email regexp check.
 *    Validation::$EMAIL_FORMAT
 *    
 *  In order to use these methods you have to define
 *  the validation array in the constructor of your FastFM class.
 *  
 *  Example:
 *  class User {
 *    public function __construct() {
 *      $this -> validation = array('name' => Validation::$PRESENCE,
 *      							'email' => array(Validation::$PRESENCE, Validation::$EMAIL_FORMAT),
 *      							'username' => Validation::$UNIQUENESS); 
 *    }
 *  }
 *  
 *  The array key is the field you want to validate on and the value is the validation function.
 *  In order to have more than one validation for a given field
 *  you must pass in a sub-array as a value (see email example).
 *  
 *  
 *  If you need more validation, you can define your own custom functions to be validated on.
 *  Validation::$CUSTOM_FUNCTION = 'function on your model'
 *  
 *  The function defined on your model receives one parameter, the validation object on which
 *  you add errors per your validation.
 *  
 *  Example:
 *  Validation::$CUSTOM_FUNCTION = 'username_no_spaces'
 *  
 *  class User {
 * 	  // Checks if there are blank spaces inside of username
 *    public function username_no_spaces($validation) {
 *		$pos = strpos($value, ' ');
 *		// if some spaces were found
 *		if ($pos){
 *			$this -> addError("Username", "Username can't have spaces");
 *			return false;
 *		}		      
 *    }
 *  }
 *  
 *  ==========
 *  That's it!
 *  ==========
 *  That's it for now!
 *  But don't worry, FastFM is under active development
 *  and there will be more updates soon!
 *  
 *  You can follow me on github (username laspluviosillas)
 *  if you are interested in contributing.
 */





//== code starts here ==
/**
 *  ==========
 *  Fast Model
 *  ========== 
 *    Base class that all ORM Models extend from.
 */
class FastModel {
		
	/** 
	 * Default Filemaker layout that will be queried
	 * for this class. Example: TrainSession.class.php
	 * has a default layout of TrainSession.	
	*/		
	public static $layout;
	
	/** 
	 *  Primary field of the object in Filemaker.
	 *  Used for the find function.
	 *  Example: TrainSession -> uniqueID = 'SessionID'.
	 */
	public static $id = 'id';

	/** FileMaker DB fields are stored in this hash. **/
	public $fields = array();
	
	/**
	 * Hash that contains fields mapped to their validation functions
	 * to be used by the Validation.
	 *    
	 * Example:
	 * array('firstName' => Validation::$PRESENCE, 
	 *       'lastName'  => Validation::$PRESENCE,											
	 *	     'email'     => array(Validation::$EMAIL_FORMAT, Validation::$UNIQUENESS),										  
	 *		);
	 */	
	public $validation;

	/** Contains validation errors after a call to validate(). **/
	public $errors;
	
	/** Determines whether model already exists in the database or not **/
	public $persisted = false;
	
	
	/**
	 *  Original FilemakerRecord id from fin queries. 
	 *  Keeping this object in memory prevents an extra find query
	 *  from being needed on the update() and save() functions.
	 **/
	public $record_id;
	
	/** Stores variables that have been updated,
	 *  Used when saving or updating object in order
	 *  to only send the variables that need to be updated
	 *  instead of the entire object.
	 **/
	public $dirty = array();
	
	/** Instantiates variables for any attributes sent in **/
	function __construct($attributes = array()) {
		foreach($attributes as $key => $value) {
			$this -> {$key} = $value;
		}
	}
	
	
	/** Dynamic getters and setters for filemaker fields **/
	public function __get($name) {
		return $this -> fields[$name];	
	}
	
	public function __set($name, $value) {
		// set field to dirty if model is persisted and field has changed.
		if($this -> fields[$name] != $value) {
			$this -> dirty[$name] = true;
		}
		
		$this -> fields[$name] = $value;
	}	
	
	/** Resets dirty tracking array.
		Called after an update or save **/
	 private function clean_dirt() {
	 	$this -> dirty = array();
	 }
	 
	 /** Returns true if dirty array is not of size 0 **/
	 public function is_dirty() {
	 	return sizeof($this -> dirty) != 0;
	 }

	/**	
	 * Returns validation object based on the 'validation' hash.
	 */ 	
	public function validate() {		
		$validation = new Validation();
		$validation -> setModel($this);
		$validation -> validate();		
		$this -> errors = $validation -> getErrors();
		return $validation;		
	}
	
	/** Returns boolean value if the model doesn't have
		any errors from its last validation check through
		validate().
	*/
	public function is_valid() {
		return sizeof($this -> errors) == 0;
	}
		
	/** Saves changed fields on object to the database. **/
	public function update() {										
		// throw an exception if the object is a new object
		// and doesn't correspond to an already-existing database
		// record.
		if(!$this -> persisted) { 
			throw new ModelException('Update cannot be called on non-persisted objects');
			return true;
		}
				
		// if the object is not dirty, there is nothing to update!
		if(!$this ->  is_dirty()) {						
			return true;
		}
		
		// validate values before update!
		$this -> validate();		
		
		// return false if invalid because update failed.
		if(!$this -> is_valid()) {			
			return false;
		}
		
		$query = new FilemakerQuery(static::$layout);
								
		// Get record for this object.
		// Use record_id if present since it is a faster search
		// otherwise use unique id defined on the table. 
		// Which is not the same as the Filemaker unique id, strangely enough.		
		if($this -> record_id != null) {
			$record = $query -> findById($this -> record_id);						
		} else {
		// If no record_id, do standard find command on model's unique id.			
			$id = $this -> get_id();			
			$record = $query -> findFirst(array(static::$id =>$id)) -> getFirstRecord();			
		}
				
		// serialize object to array to send into
		// filemaker query.
		$criteria = $this -> to_dirty_array();
		
		// perform update
		$query = new FilemakerQuery(static::$layout);
		$query -> update($criteria, $record);
		
		// clean dirty array since model was just updated.
		$this -> clean_dirt();
		
		// everything was successful! (in theory).
		return true;
	}
	
	/** Extension of update() above. Receives a hash of attributes
	 *  populates the model with those attributes and then calls
	 *  update().	 
	 */
	public function update_attributes($attributes) {
		$this -> set_attributes($attributes);
		return $this -> update();
	}

	/** Save is really just an alias for create and update.
	 *  Create if model is not persisted.
	 *  Update if it is.
	 *  
	 *  Example:
	 *  
	 *  Create example:
	 *  $login = new Login();
	 *  $login -> name = 'James Strong'
	 *  $login -> email = 'james.strong@etherpros.com'
	 *  $login -> save();
	 *  // returns true or false.
	 *  
	 *  Update example:
	 *  $login = Login::find('1');
	 *  $login -> name = 'I have a new name!';
	 *  $login -> save();
	 *  // also returns true or false.
	 **/
	public function save() {
		// if persisted just route to update();
		if($this -> persisted) {
			return $this -> update();
		} else {
		// perform create.
		// In the case of create, since dirty-tracking
		// is not applicable, the entire fields array
		// is sent as criteria to be saved.
			$criteria = $this -> fields;
			$this -> validate();
			if($this -> is_valid()) {
				$query = new FilemakerQuery(static::$layout);
				$query -> create($criteria);
				return true;
			}
			//if invalid return false
			return false;			
		}
	}
	
	/** Static create. Calls sets attributes on a new ORM model instance and then calls save() on it.
	 *  Example:
	 *  $login = Login::create('name' => 'James Strong', 'email' => 'james.strong@etherpros.com');
	 *  if($login -> is_valid()) {
	 *  	echo 'login created';
	 * 	} else {
	 * 		print_r($login -> errors);
	 *  }
	**/
	public static function create($criteria) {		
		$instance = self::instantiate();
		$instance -> set_attributes($criteria);
		$instance -> save();		
		return $instance;
	}
	
	private function set_attributes($attributes) {
		foreach($attributes as $key => $value) {
			// It is important to set the attribute using
			// $this -> {$key} syntax and NOT by setting
			// the fields array directly like $this -> fields[$key].
			// The latter method calls the __set function which includes
			// dirty tracking, while the former skips all the dirty tracking
			// checks.
			$this -> {$key} = $value;
		}
	}
	
	/** Returns array of all objects **/
	public static function all($layout = null) {
		if($layout == null) {
			$layout = static::$layout;
		}
		$query = new FilemakerQuery($layout);
		$records = $query -> all() -> getRecords();
		return self::unserialize_many($records);
	}
	
	/**
	 * Performs a filemaker find command based on the ID passed in.
	 * Searches on the primary field (defined by uniqueID) of the layout.
	 * Uses default layout for the model unless one is specified.
	 * Example:
	 * $login = Login::find('301');
	 */	
	public static function find($id, $layout = null) {
		if($layout == null) {
			$layout = static::$layout;
		}		
		$query = new FilemakerQuery($layout);
		$record = $query -> findFirst(array(static::$id => $id)) -> getFirstRecord();
		return self::unserializer($record);
	}
	
	/**
	 * Performs a filemaker find command based on the hash of criteria passed in. 
	 * $firstOnly returns only the first record if set to true.
	 * $layout overrides default layout to class.
	 * 
	 * Example:
	 * $logins = Login::where(array('FirstName' => 'James', 'Status' => 'Active'));
	 */
	public static function where($criteria, $firstOnly=false, $layout=null) {
		if($layout == null) {
			$layout = static::$layout;
		}					
		$query = new FilemakerQuery($layout);
		if($firstOnly) {			
			$record = $query -> findFirst($criteria) -> getFirstRecord();
			// return unserialized object
			return self::unserializer($record);
		}else{
			$records = $query -> find($criteria) -> getRecords();
			// Just return an empty array if no records were found
			if($records == null) { return array(); };
			return self::unserialize_many($records);
		}
	}
		
	/** Unserializes an array of Filemaker_Result objects **/
	private static function unserialize_many($records) {
		$results_array = array();
		foreach($records as $record) {
			array_push($results_array, self::unserializer($record));
		}
		return $results_array;
	}
	

	/** 
	 * Static version of unserialize method below.
	 * 
	 * NOTE:
	 * For some completely unknown reason, PHP
	 * doesn't allow for there two be 2 methods with the
	 * same name even though one is a static method
	 * and the other is an instance method.
	 * 
	 * Example:
	 * private static function unserialize()
	 * private function unserialize()
	 * - throws error message -
	 * 
	 * Due to this naming restriction, I had to
	 * name the class (static) level unserialize method
	 * 'unserializer' instead,
	 *
	 **/
	private static function unserializer($record) {
		$instance = self::instantiate();
		$instance -> unserialize($record);		
		return $instance;
	}
	
	/** Unserializes Filemaker_Result object and populates fields hash. **/
	private function unserialize($record) {
		$fields = $record -> getFields();
		foreach($fields as $field) {
			$this -> fields[$field] = $record -> getField($field);
		}
		
		// filemaker unique record id.
		$this -> record_id = $record -> getRecordId();
		
		// set instance to persisted since it was unserialized
		// from the database.
		$this -> persisted = true;		
	}
	
	/** Serializes all ORM fields to a a field name / value hash of changed fields.
	 *  Function is used to do update commands from an
	 *  ORM object to Filemaker.
	 *  Only returns dirty fields.  
	 */
	public function to_dirty_array() {
		$serialized = array();
		foreach($this -> dirty as $key => $value) {
			$serialized[$key] = $this -> fields[$key];			
		}
		return $serialized;
	}
	
	/** Creates new instance of this object from the static scope **/
	public static function instantiate() {
		$class = static::klass();
		$instance = new $class();
		return $instance;		
	}

	/** Simple alias for retrieving unique id **/
	public function get_id() {
		return $this -> {static::$id};
	}
	
	/** Simple alias for retrieving the class name **/
	public static function klass() {
		return get_called_class();
	}
		
}

/**
 * 
 * ===============
 * Filemaker Query
 * ==============
 *   Filemaker Query is just a helper class that
 *   adds a layer of abstraction for performing filemaker commands.
 *   This class is heavily used by FastModel for its basic ORM queries.
 * 
 */
class FilemakerQuery {
	private static $VALID = "VALID";
	private static $BLANK = "BLANK";
	private static $ERROR = "ERROR";
	
	public $connection = null;
	public $layout = null;
	public $sortRules = null;
	
	public function __construct($layout) {				
		$this -> connection = FilemakerDB::getConnection();		
		$this -> layout = $layout;
	}	
	public function setSortRules($sortRules) {
		$this -> sortRules = $sortRules;
	}
		
	/** Basic update command. Receives a hash of parameters 
	 *  and returns a Filemaker_Result object.
	 *  Example:  
	 *  $query = new FilemakerQuery('Logins');
	 *  $criteria = array('FullName' => 'James Strong');
	 *  $record = $query -> findFirst($criteria) -> getFirstRecord();
	 *  $query -> update(array('FullName' => 'John Strong'));
	 */
	public function update($criteria, $record) {
		foreach($criteria as $key=>$value) {
			//echo $key . " " . $value . "\n";
			$record -> setField($key, $value);				
		}		
		$result = $record -> commit();
		return $result;
	}
	
	/** Basic create command. Receives a hash of parameters 
	 *  and returns a Filemaker_Result object.
	 *  Example:
	 *	$query = new FilemakerQuery('NewLMSCopy');
	 *	$query -> create(array("Login_ID" => 'id value'));
	 */
	public function create($criteria) {
		$record =& $this -> connection -> newAddCommand($this -> layout, array());
		foreach($criteria as $key => $value) {
			$record -> setField($key, $value);
		}
		$result = $record -> execute();					
		return $result;				
	}
	
	/** Same as find command, just with a max cap of one **/
	public function findFirst($criteria) {
		return $this -> find($criteria, 1);
	}
	
	/** Performs a Find All FileMaker command **/
	public function all() {
		$findCommand =& $this -> connection -> newFindAllCommand($this -> layout);
		$result = $findCommand -> execute();
		$this -> validateResult($result);
		return $result;
	}	
	
	/** Equivalent to FileMaker's getRecordById function **/
	public function findById($id) {
		$result = $this -> connection -> getRecordById($this -> layout, $id);
		$this -> validateResult($result);
		return $result;		
	}
	
	/** Basic find command. Receives a hash of parameters 
	 *  and returns a Filemaker_Result object.
	 *  
	 *  $criteria: Hash of values used for the find command.
	 *  $max: Max number of records to be retrieved. -1 means no cap.
	 *  
	 *  Compound finds are performed by passing in arrays of arrays.  
	 *
	 *  Examples:
	 *  $query = new FilemakerQuery('Logins');	 
	 *  $result = $query -> find(array('LastName' => 'Strong')
	 *  
	 *  Compound find example:
	 *	$query = new FilemakerQuery('TrainSession');
	 *	$criteria = array(
	 *						array("Login_ID" => "=".$loginID, "Status" => "Assigned"),
	 *						array("Login_ID" => "=".$loginID, "Status" => "Live")		
	 *					 );
	 *	$result = $query -> find($criteria, -1);
	 */  	
	public function find($criteria, $max = -1, $loud=false) {
		// if the array is multi-dimensional, this is a compound find.
		$compound = is_array($criteria[0]);
				
		if(!$compound) {
			$findCommand =& $this -> connection -> newFindCommand($this -> layout);
			//if not a compound find command, then add the criteria directly to the find command.
			foreach($criteria as $key => $value) {
				$findCommand -> addFindCriterion($key, $value);
			}
		} else {
			$findCommand =& $this -> connection -> newCompoundFindCommand($this -> layout);
			//if a compound find command, then create find requests, add criteria to the requests.
			//then add the requests to the find command.
			$i = 0;
			foreach($criteria as $set) {
				$i++;
				$findRequest =& $this -> connection -> newFindRequest($this -> layout);								
				foreach($set as $key => $value) {
					$findRequest -> addFindCriterion($key, $value);					
				}
				$findCommand -> add($i, $findRequest);
			}
		}
		// Set max number of records if present.
		if($max != -1) {
			$findCommand -> setRange(0, $max);
		}
		// Set sort rules.
		if($this -> sortRules) {
			$i = 0;
			foreach($this -> sortRules as $key => $value) {
				$i++;
				$findCommand -> addSortRule($key, 1, $value);				 
			}
		}
		$result = $findCommand -> execute();
		//HACK: return empty result object if no records found instead of a filemaker error.
		if($this -> validateResult($result) == self::$BLANK && $loud == false) {			
			return new Filemaker_Result($this -> connection);
		}		
		return $result;		
	}
	
	/** Checks Filemaker_Result to make sure it is not an error.
	 *  When loud is set to true, this function will throw
	 *  an exception if the result is an error.
	 *  
	 *  When loud is set to false, the function will
	 *  only return true or false depending on whether
	 *  the result is an error or not.
	 *  
	 *  Error 401 has been overriden to return BLANK instead of ERROR.
	 *  Exceptions are only thrown for critical errors that shouldn't happen.
	 *  On the other hand, 401 errors can happen all the time if a search
	 *  query doesn't return any results. This is normal behavior and should not throw an exception.
	 */
	public function validateResult($result, $loud=true) {
		$isError = FileMaker::isError($result);
		if($isError == true) {
			// 401 is a no record found error, so we don't want to throw a model exception for this.
			if ( $result->getCode() == "401" ) {				
				return self::$BLANK;						
			}else{
				//only throw an exception if this is a loud validation
				if($loud) {
					throw new ModelException("Error retrieving information from database: " . $result -> getCode(), 
											  $result -> getCode());
				}
				return self::$ERROR;
			}			
		}
		if($isError == false) {
			return self::$VALID;	
		}				
	}	
}

/** 
* ============
* Filemaker DB
* ============
*   Singleton that lazy-loads connection to FileMaker DB 
**/
class FilemakerDB {
	// database connection details
	public static $host = 'localhost';
	public static $database = null;
	public static $username = '';
	public static $password = '';
	
	private static $connection 	= null;
	
	// Private constructor for singleton pattern
	private function __construct() {
	}
	
	/** Values required for setting up the connection. **/
	public static function setup($host, $database, $username, $password) {
		self::$host = $host;
		self::$database = $database;
		self::$username = $username;
		self::$password = $password;
	}
	
	// lazy-loads filemaker database connection
	public static function getConnection() {		
		if (!self::$connection)
		{			
			self::$connection = new FileMaker();
			self::$connection->setProperty('database', self::$database);
			self::$connection->setProperty('hostspec', self::$host);
			self::$connection->setProperty('username', self::$username);
			self::$connection->setProperty('password', self::$password);

		}
		return self::$connection;
	}

	// clone private for singleton pattern
	private function __clone(){
	}
}

/* Stub exception class for Filemaker Model exceptions */
class ModelException extends Exception {}



/**
 * 
 * ===========
 * Validation
 * ===========
 * Class for model-level validation.
 *  
 */
class Validation { 		
	public static $PRESENCE = "presenceValidation";
	public static $UNIQUENESS = "uniquenessValidation";
	public static $EMAIL_FORMAT = "emailFormatValidation";
	
	/*
	
 	$CUSTOM_FUNCTION is an exception.
    All of the constants defined above call functions that are defined in this class.
	
	$CUSTOM_FUNCTION instead calls a function defined in the MODEL being validated.
	
	Pseudo-code example:
	class MyCustomClass extends FastModel {
	    public static $validation = array(Validation::$CUSTOM_FUNCTION, 'iWillAlwaysFail');
	    public function iWillAlwaysFail($validation) {
	      $validation -> addError('Always Fail', 'I am a custom function that always fails');
	    }
	}
		
	When validate() is called on an instance of MyCustomClass, 
	iWillAlwaysFail which is defined in our MyCustomClass will be called, 
	and the validation will return "I am a custom function that always fails".
	
	*/		
	public static $CUSTOM_FUNCTION = "CustomValidationFunctionForModel";
	
	private $model = null;
	private $action = null;		
	private $errors = array();

	/** Goes through the validation array inside of $model
	 *  and calls the requires validation functions.
	 *  
	 *  Refer to FastModel for details on how the validation hash is defined.
	 *  
	 *  Example:
	 *  $attendee = new Attendee();
	 *  
	 *  // Is going to fail, because Attendee requires 
	 *  // first name and last name presence.
	 *  $attendee -> setFirstName('');  
	 *  $attendee -> setLastName('');
	 *  $attendee -> setEmail('totally@unique.com');
	 *  
	 *  $validation = new Validation();
	 *  $validation -> setModel($login);
	 *  $validation -> validate();
	 *  
	 *  // Returns First Name and Last Name can't be blank. 
	 */
	public function validate() {										
		foreach($this -> model -> validation as $key => $value) {
			if( is_array($value) ) {
				foreach ($value as $entry) {
					$this -> validation_call($key, $entry);
				}
			}else{
				$this -> validation_call($key, $value);				
			}				
		}
	}	
	
	private function validation_call($key, $value) {
		// if the model is persisted to the database
		// then we only want to call validation on
		// dirty fields, since we know that non-dirty fields are valid.
		// So, if the model is persisted but the field being validated is NOT
		// dirty, skip validation call.
		if($this -> model -> persisted && $this -> model -> dirty[$key] != true) {
			return;
		}
		
		if($key != self::$CUSTOM_FUNCTION) {														
			$this -> $value($key);
		} else {													
			$this -> model -> $value($this);
		}
	}
	
	private function uniquenessValidation($key) {	
		$value = $this -> model -> { $key };
		// uniqueness validation does NOT manage blank values.
		// doing a record search with a blank value returns all records.
		// so the uniqueness will always fail.
		if(!empty($value)) {	
			$class = $this -> model -> klass();
			$this -> checkUniquenessFor($key, $value, $class::$layout);
		}
	}
	private function presenceValidation($key) {						
		$value = $this -> model -> {$key};		
		$this -> checkPresence($key, $value);			
	}
	private function emailFormatValidation($key) {		
		$value = $this -> model -> {$key};
		$this -> checkEmail($value);
	}

	public function validatePasswordEquality($pwd, $pwd_confirm) {
		// if password values do not match						
		if($pwd != $pwd_confirm) {										
			//send error of passwords not matching.
			$this -> addError('Password', "Password and Password confirmation fields do not match.");
			return false;
		}			
		return true;
	}		
	public function checkPresence($key, $value) {		
		if(empty( $value )) {
			$this -> addError($key, $key . " can't be blank");
			return false;
		}
		return true;
	}
	
	public function checkEmail($email) {
		if(!empty($email)) {
			$result = TRUE;
			if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
				$result = FALSE;
			}
			if(!$result) {
				$this -> addError('Email', "Email is not a valid format.");			  	
			}			
			return $result;
		}	  
	}
	
	public function checkUniquenessFor($key, $value, $table) {
		//escape @ characters 
		$value = str_replace("@", "\@", $value);
		$query = new FilemakerQuery($table);
		$records = $query -> findFirst(array($key => "=".$value)) -> getRecords();			
		$record_count = 0;
		foreach ((array) $records as $record) {
			$record_count++;
		}
		//return true of false depending on whether field is already present in the DB.
		$unique = ($record_count > 0) ? false : true;
		if(!$unique) $this -> addError($key, "This " . $key . " is already taken.");		
		return $unique; 			
	}		

	// ===================
	// Getters and Setters
	// ===================		
	public function setModel($model) { $this -> model = $model; }
	public function getModel() { return $model; }
				
	public function getErrors() {
		return $this->errors;
	}
	public function addError($key,$value) {			
		array_push($this->errors, new ValidationError($key, $value));			
	}
	public function isValid() {
		return sizeof($this->errors) == 0;
	}
	public function numberOfErrors() {
		return sizeof($this->errors);
	}
	public function setErrors($errors) {
		$this->errors = $errors;
	}		
	
}

class ValidationError {
	public $key = "";
	public $value = "";
	public function __construct($key,$value) {
		$this -> key = $key;
		$this -> value = $value;
	}
}