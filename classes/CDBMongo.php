<?php
	
	include_once 'CSettings.php';

	$mongo = new CDBMongo();

	class CDBMongo
	{
		//stores the mongo db
		protected	$dbmongo;
		//stores session data results
		private		$session;
		
		private		$defaultConnection = 'smith';

		public function __construct($config = array())
		{
			 //initialize the database
        	$this->_init($config);
		}

		/**
	     * Initialize MongoDB. There is currently no support for persistent
	     * connections.  It would be very easy to implement, I just didn't need it.
	     *
	     * @access  private
	     * @param   array   $config
	     */
    	private function _init($config)
    	{
	        if(!empty($config)) CSettings::$MONGO_CONNECTION_POOL = $config;
	        
	        //generate server connection strings
	        $connections = array();
	        
	        if(!empty(CSettings::$MONGO_CONNECTION_POOL))
	        {
	            foreach(CSettings::$MONGO_CONNECTION_POOL as $server=>$value)
	            {
	                $str = '';
	                if(!empty($value[2]) && !empty($value[3])) $str .= $value[2] . ':' . $value[3] . '@';
	                
	                $str .= $value[0] . ':' . $value[1];
	                array_push($connections, $str);
	            }
	        }
	        else
	        {
	            //use default connection settings
	            array_push($connections, 'localhost:27017');
	            
	            CSettings::$MONGO_CONNECTION_POOL[$this->defaultConnection][0] = 'localhost';
	            CSettings::$MONGO_CONNECTION_POOL[$this->defaultConnection][1] = '27017';
	            // username
	            CSettings::$MONGO_CONNECTION_POOL[$this->defaultConnection][2] = '';
				// password
	            CSettings::$MONGO_CONNECTION_POOL[$this->defaultConnection][3] = '';
	            // DB name
	            CSettings::$MONGO_CONNECTION_POOL[$this->defaultConnection][4] = $this->defaultConnection;
	            // persistence
	            CSettings::$MONGO_CONNECTION_POOL[$this->defaultConnection][5] = false;
	        }
	        
	        //load mongo servers
	        $this->dbmongo = new Mongo('mongodb://' . implode(',', $connections));
    	}
    	
    	public function connect($conn)
    	{
    		if(CSettings::$MONGO_CONNECTION_POOL[$conn][6])
    		{
    			return CSettings::$MONGO_CONNECTION_POOL[$conn][6];
    		}
    		
    		//load db
    		try
    		{
    			$dbmongo = $this->dbmongo->selectDB(CSettings::$MONGO_CONNECTION_POOL[$conn][4]);
    		}
    		catch(InvalidArgumentException $e)
    		{
    			throw new Exception('The MongoDB database specified in the config does not exist.');
    		}

    		CSettings::$MONGO_CONNECTION_POOL[$conn][6] = $dbmongo;
    		return $dbmongo;
    	}

		public function insert($conn, $coll, $f, $options=array())
		{
			$db = $this->connect($conn);
			
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
			
			return $coll->insert($f, $options);
		}
		
		/**
		 * @param array $id
		 */
		public function getAllData($conn, $coll, $id, $fields=array())
		{
			$db = $this->connect($conn);
				
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
				
			$cursor = $coll->find($id, $fields);
			
			if($cursor)
			{
				return $cursor;
			}
			else
			{
				return array();
			}
		}
		
		/**
		 * @param array $id
		 */
		public function getOneData($conn, $coll, $id)
		{
			$db = $this->connect($conn);
			
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
			
			$result = $coll->findone($id);
			if($result)
			{
				return $result;
			}
			else
			{
				return array();
			}
		}

		/**
		 * @param array $id
		 */
		public function getDataFile($conn, $coll, $id)
		{
			$db = $this->connect($conn);
				
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
			
			$result = $coll->find($id);
			if($result)
			{
				return $result;
			}
			else
			{
				return 0;
			}
		}
		
		/**
		 * @param string $key
		 * @param string $keyVal
		 * @param array $data
		 * @param array $options
		 * $options = array(
		      'upsert'  => true,
		      'safe'    => MongoSession::SAFE,
		      'fsync'   => MongoSession::FSYNC
		    );
		 */
		public function update($conn, $coll, $key, $keyVal, $data, $options)
		{
			$db = $this->connect($conn);
				
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
			
    		$query = array($key => $keyVal);
			//perform the update or insert
		    try
		    {
		    	$coll->update($query, array('$set' => $data), $options);
		    }
		    catch(Exception $e)
		    {
		    	return false;
		    }
		 	return true;
		}

		public function storeFile($conn, $coll, $htmlpostfile, $metadata)
		{
			$db = $this->connect($conn);
				
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
			
			$grid = $coll->getGridFS();
			$grid->storeUpload($htmlpostfile, $metadata);
		}

		public function delete($conn, $coll, $key, $one = array())
		{
			$db = $this->connect($conn);
				
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
			
			$result = $coll->remove($key, $one);
			
			return $result;
		}

		public function ensureIndex($conn, $coll, $args)
		{
			$db = $this->connect($conn);
				
			//load collection
			try
			{
				$coll = $db->selectCollection($coll);
			}
			catch(Exception $e)
			{
				throw new Exception('The MongoDB collection specified in the config does not exist.');
			}
			
			return $coll->ensureIndex($args);
		}
	}
