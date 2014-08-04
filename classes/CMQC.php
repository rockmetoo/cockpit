<?php

	$pad = new CMQC();

	class CMQC
	{
		private $context;
		private $client;	//Socket to broker
		private $timeout;	//Request timeout
		private $retries;	//Request retries
		private $pad_servers_conn = NULL;
		private $pad_default_conn = 'default';
		private $daemon_connections_pool = array(
			'default'	=> array('tcp://localhost:5555', NULL),
			'localhost'	=> array('tcp://localhost:5555', NULL),
		);
		
		private $pack_header = "\0\0\0\0\0\0\0\0"; //8 byte null
		private $pack_footer = "\0\0\0\0\0\0\0\0"; //8 byte null

		public function __construct($conn = '')
		{
			if($conn) $this->pad_servers_conn = $conn;
			else $this->pad_servers_conn = $this->pad_default_conn;
		}

		public function connect($conn, $timeout = 2500)
		{
			if($this->daemon_connections_pool[$conn][1])
			{
				return $this->daemon_connections_pool[$conn][1];
				exit;
			}
			
			if($this->client)
			{
				unset($this->client);
			}

			$this->timeout = $timeout;	//msecs
			$this->context = new ZMQContext();
			$this->client = new ZMQSocket($this->context, ZMQ::SOCKET_REQ);
			$this->client->connect($this->daemon_connections_pool[$conn][0]);
			//Configure socket to not wait at close time
			$this->client->setSockOpt(ZMQ::SOCKOPT_LINGER, 0);
			$this->daemon_connections_pool[$conn][1] = $this->client;
		}

		public function sendRaw($connection, $string_data, $timeout = 2500)
		{
			try
			{
				$this->connect($connection, $timeout);
				$this->client->send($this->pack_header . $string_data);
				return self::recvRaw($this->client);
			}
			catch(ZMQSocketException $e)
			{
				if($e->getCode() === ZMQ::ERR_EAGAIN)
				{
					//unable to execute the operation. try again
					return 0;
		        }
		        else
		        {
		        	//Error, hopefully connection failed due to daemon not running or
		        	//Internet connectivity error
		        	return -1;
		        }
			}
		}

		public function recvRaw($conn)
		{
			$poll = new ZMQPoll();
			$poll->add($conn, ZMQ::POLL_IN);
			$events = $poll->poll($read, $write, $this->timeout);
			if($events>0)
			{
				return $this->client->recv();
			}
			else
			{
				echo "Warning: permanent error, abandoning request", PHP_EOL;
				return; // Give up
			}
		}

		public function setTimeout($timeout)
		{
			$this->timeout = $timeout;
		}
	}