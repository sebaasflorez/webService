<?php

require_once "Config.php";

class Modelo
{
	private $host, $user, $pass, $db_name;

	/**
	 * MySQL connection information
	 *
	 * @var resource
	 */

	// private $link;
	public $link;
	/**
	 * Result of last query
	 *
	 * @var resource
	 */

	private $result;

	/**
	 * Constructor
	 *
	 * @param string $host MySQL host address
	 * @param string $user Database user
	 * @param string $password Database password
	 * @param string $db Database name
	 * @param boolean $persistant Is persistant connection
	 * @param  boolean $connect_now Connect now
	 * @return void
	 */
	public function __construct($persistant = true, $connect_now = true)
	{
		$this->host = DB_HOST; // Host address
		$this->user = DB_USER;	// User
		$this->pass = DB_PASS;	// Password
		$this->db_name = DB_NAME;	// Database

		if ($connect_now)
			$this->connect($persistant);

		return;
	}

	/**
	 * Destructor
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Connect to the database
	 *
	 * @param boolean $persist Is persistant connection
	 * @return boolean
	 */
	public function connect($persist = true)
	{
		if ($persist)
			$link = mysqli_connect('p:'.$this->host, $this->user, $this->pass);
		else
			$link = mysqli_connect($this->host, $this->user, $this->pass);

		if (mysqli_connect_errno()) {
            printf("Conexion fallida: %s\n", mysqli_connect_error());
            return false;
        }

		if (!$link)
			trigger_error('Could not connect to the database.', E_USER_ERROR);

		if ($link)
		{
			$this->link = $link;
			if (mysqli_select_db($link,$this->db_name))
				return true;
		}

		return false;
	}

	/**
	 * Query the database
	 *
	 * @param string $query SQL query string
	 * @return resource MySQL result set
	 */
	public function query($query)
	{	

            
            $result = mysqli_query($this->link,$query);
            $affected = $this->affectedRows();
            $this->result = $result;
          
            if ($result == false or  $affected  <= 0){
            	
            	return false;
            	trigger_error('Uncovered an error in your SQL query script: "' . $this->error() . '"');
           	}else{

            	return $this->result;		
            }
 
            
	}

	/**
	 * Update the database
	 *
	 * @param array $values 3D array of fields and values to be updated
	 * @param string $table Table to update
	 * @param string $where Where condition
	 * @param string $limit Limit condition
	 * @return boolean Result
	 */
	public function update(array $values, $table, $where = false, $limit = false,$operacion = "")
	{
		if (count($values) < 0)
			return false;
			
		$fields = array();
		foreach($values as $field => $val){
				$fields[] = "`" . $field . "` = '" . $this->escapeString($val) . "'";
		}

		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';

		if ($this->query("UPDATE `" . $table . "` SET " . implode($fields, ", ") . $where . $limit))
			return true;
		else
			return false;
	}
	

	/**
	 * Insert one new row
	 *
	 * @param array $values 3D array of fields and values to be inserted
	 * @param string $table Table to insert
	 * @return boolean Result
	 */
	public function insert(array $values, $table)
	{
		if (count($values) < 0)
			return false;
	
		foreach($values as $field => $val)
			$values[$field] = $this->escapeString($val);

		if ($this->query("INSERT INTO `" . $table . "`(`" . implode(array_keys($values), "`, `") . "`) VALUES ('" . implode($values, "', '") . "')"))
			return true;
		else
			return false;
	}

	/**
	 * Select
	 *
	 * @param mixed $fields Array or string of fields to retrieve
	 * @param string $table Table to retrieve from
	 * @param string $where Where condition
	 * @param string $orderby Order by clause
	 * @param string $limit Limit condition
	 * @return array Array of rows
	 */
	public function select($fields, $table, $where = false, $orderby = false, $limit = false)
	{
		if (is_array($fields))
			$fields = "" . implode($fields, ",") . "";


		$orderby = ($orderby) ? " ORDER BY " . $orderby : '';
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';

		$this->query("SELECT " . $fields . " FROM " . $table . "" . $where . $orderby . $limit);

		if ($this->countRows() > 0)
		{
			$rows = array();

			while ($r = $this->fetchAssoc()){
				$rows[] = $r;
			}
				
			return $rows;
		
			
		} else
			return false;
	}

	/**
	 * Selects one row
	 *
	 * @param mixed $fields Array or string of fields to retrieve
	 * @param string $table Table to retrieve from
	 * @param string $where Where condition
	 * @param string $orderby Order by clause
	 * @return array Row values
	 */
	public function selectOne($fields, $table, $where = false, $orderby = false)
	{
		$result = $this->select($fields, $table, $where, $orderby, '1');

		return $result[0];
	}
	
	/**
	 * Selects one value from one row
	 *
	 * @param mixed $field Name of field to retrieve
	 * @param string $table Table to retrieve from
	 * @param string $where Where condition
	 * @param string $orderby Order by clause
	 * @return array Field value
	 */
	public function selectOneValue($field, $table, $where = false, $orderby = false)
	{
		$result = $this->selectOne($field, $table, $where, $orderby);

		return $result[$field];
	}

	/**
	 * Delete rows
	 *
	 * @param string $table Table to delete from
	 * @param string $where Where condition
	 * @param string $limit Limit condition
	 * @return boolean Result
	 */
	public function delete($table, $where = false, $limit = 1)
	{
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';

		if ($this->query("DELETE FROM `" . $table . "`" . $where . $limit))
			return true;
		else
			return false;
	}

	/**
	 * Fetch results by associative array
	 *
	 * @param mixed $query Select query or MySQL result
	 * @return array Row
	 */
	public function fetchAssoc($query = false)
	{
		$this->resCalc($query);
		return mysqli_fetch_assoc($query);
	}

	/**
	 * Fetch results by enumerated array
	 *
	 * @param mixed $query Select query or MySQL result
	 * @return array Row
	 */
	public function fetchRow($query = false)
	{
		$this->resCalc($query);
		return mysqli_fetch_row($query);
	}

	/**
	 * Fetch one row
	 *
	 * @param mixed $query Select query or MySQL result
	 * @return array
	 */
	public function fetchOne($query = false)
	{
		list($result) = $this->fetchRow($query);
		return $result;
	}

	/**
	 * Fetch a field name in a result
	 *
	 * @param mixed $query Select query or MySQL result
	 * @param int $offset Field offset
	 * @return string Field name
	 */
	public function fieldName($query = false, $offset)
	{
		$this->resCalc($query);
		// return mysql_field_name($query, $offset);
		$field = mysqli_fetch_field_direct($query,$offset);
		return $field->name;
	}

	/**
	 * Fetch all field names in a result
	 *
	 * @param mixed $query Select query or MySQL result
	 * @return array Field names
	 */
	public function fieldNameArray($query = false)
	{
		$names = array();

    	$field = $this->countFields($query);

    	for ( $i = 0; $i < $field; $i++ )
			$names[] = $this->fieldName($query, $i);

		return $names;
	}

	/**
	 * Free result memory
	 *
	 * @return boolean
	 */
	public function freeResult()
	{
		return mysqli_free_result($this->result);
	}

	/**
	 * Add escape characters for importing data
	 *
	 * @param string $str String to parse
	 * @return string
	 */
	public function escapeString($str)
	{
		return mysqli_real_escape_string($this->link,$str);
	}

	/**
	 * Count number of rows in a result
	 *
	 * @param mixed $result Select query or MySQL result
	 * @return int Number of rows
	 */
	public function countRows($result = false)
	{
		$this->resCalc($result);
		return (int) mysqli_num_rows($result);
	}

	/**
	 * Count number of fields in a result
	 *
	 * @param mixed $result Select query or MySQL result
	 * @return int Number of fields
	 */
	public function countFields($result = false)
	{
		$this->resCalc($result);
		// return (int) mysql_num_fields($result);
		return (int) mysqli_field_count($this->link);
	}

	/**
	 * Get last inserted id of the last query
	 *
	 * @return int Inserted in
	 */
	public function insertId()
	{
		return (int) mysqli_insert_id($this->link);
	}

	/**
	 * Get number of affected rows of the last query
	 *
	 * @return int Affected rows
	 */
	public function affectedRows()
	{
		return (int) mysqli_affected_rows($this->link);
	}

	/**
	 * Get the error description from the last query
	 *
	 * @return string
	 */
	public function error()
	{
		return mysqli_error($this->link);
	}

	/**
	 * Dump MySQL info to page
	 *
	 * @return void
	 */
	public function dumpInfo()
	{
		echo mysqli_info($this->link);
	}

	/**
	 * Close the link connection
	 *
	 * @return boolean
	 */
	public function close()
	{
		return mysqli_close($this->link);
	}

	/**
	 * Determine the data type of a query
	 *
	 * @param mixed $result Query string or MySQL result set
	 * @return void
	 */
	private function resCalc(&$result)
	{
		if ($result == false)
			$result = $this->result;
		else {
			if (gettype($result) != 'resource')
				$result = $this->query($result);
		}

		return;
	}
}
