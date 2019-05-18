<?php

namespace Epsiloncool\Utils;

class DB_Semaphore
{
	public $db;
	public $prefix;
	public $timeout = 60;
	public $process_id;
	public $proc_name;

	function __construct($db, $prefix = 'dbsem', $process_id, $proc_name)
	{
		$this->db = $db;
		$this->prefix = $prefix;
		$this->process_id = $process_id;
		$this->proc_name = $proc_name;
	}

	/**
	 * Check if we have privileges
	 */
	function Check()
	{
		$db = $this->db;

		$time = time();

		// Check if we have a priority to run this application
		$q = 'select * from `'.$this->prefix.'_locks` where ident = "'.addslashes($this->proc_name).'" and expire_dt > "'.date('Y-m-d H:i:s', $time).'" and process_id = "'.addslashes($this->process_id).'"';
		$r8 = $db->query($q);
		
		if ($r8->num_rows > 0) {
	
			// Checking new lock...
			//$row8 = $r8->fetch_assoc();
	
			return true;
		} else {
			// Another application has catched privileges. Finishing.
			return false;
		}
	}

	/**
	 * Reserve a semaphore (is possible)
	 */
	function Enter()
	{
		$db = $this->db;

		$time = time();

		$db->query('lock tables `'.$this->prefix.'_locks` write');

		$q = 'select * from `'.$this->prefix.'_locks` where ident = "'.addslashes($this->proc_name).'" and expire_dt > "'.date('Y-m-d H:i:s', $time).'"';
		$r8 = $db->query($q);
	
		if ($r8->num_rows > 0) {
	
			//$row8 = $r8->fetch_assoc();
		
			// The lock already exists
			$db->query('unlock tables');

			// The semaphore already taken
			return false;
		} else {
			// We can set up our own rules
			$q = 'replace `'.$this->prefix.'_locks` (`expire_dt`, `process_id`, `ident`) values ("'.date('Y-m-d H:i:s', $time + intval($this->timeout)).'", "'.addslashes($this->process_id).'", "'.addslashes($this->proc_name).'")';
			$db->query($q);
	
			$db->query('unlock tables');
	
			return $this->Check();
		}
	}

	/**
	 * Release the lock 
	 */
	function Leave()
	{
		$db = $this->db;

		$q = 'delete from `'.$this->prefix.'_locks` where ident = "'.addslashes($this->proc_name).'" and process_id = "'.addslashes($this->process_id).'"';
		$db->query($q);
	}

	function Update()
	{
		$db = $this->db;

		$time = time();

		// Update semaphore
		$q = 'replace `'.$this->prefix.'_locks` (`expire_dt`, `process_id`, `ident`) values ("'.date('Y-m-d H:i:s', $time + intval($this->timeout)).'", "'.addslashes($this->process_id).'", "'.addslashes($this->proc_name).'")';
		$db->query($q);
	}
}
