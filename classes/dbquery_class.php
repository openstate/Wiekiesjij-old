<?php

	/*** DBQuery Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1149257784703_960700_669 ***/

	/** + Class Description +
		*
		* This is a helper class facilitating database communications.
		**/

	require_once("config_class.php");

	class DBQuery
	{
		/*+*+*+*+*+*+*+*+*  attributes *+*+*+*+*+*+*+*+*/

		/** - type: resource
			* This attribute points to the result of the last query or contains 'false'
			* when an error occured.
			**/
		var $result;

		/** - type: boolean
			* This attrbute indicates whether a database connection has been established.
			**/
		var $connected = false;

		/** - type: boolean
			* This attribute indicates whether the PHP script should be killed when
			* a database error occurs.
			**/
		var $dieOnError  = false;

		/*+*+*+*+*+*+*+*+*  associations *+*+*+*+*+*+*+*+*/
		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			* - param query: string
			* 	An SQL query.
			*
			* The constructor. It also executes a query if one is given.
			**/
		function DBQuery($query = "")
		{//_9_5_1_5e60209_1149259380093_698037_934
			if($query != "") {
				$this->query ($query);
			}
		}//_9_5_1_5e60209_1149259380093_698037_934

		/** - return type: void
			* - param message: string
			* 	The message to be shown.
			*
			* Error processing method. It writes a message on error, and kills the
			* script if dieOnError is set to 'true'.
			**/
		function error($message)
		{//_9_5_1_5e60209_1149260001546_219875_940
			if($this->dieOnError) {
				DBQuery::dump($message);
				die();
			}
			else {
				DBQuery::dump($message);
			}
		}//_9_5_1_5e60209_1149260001546_219875_940

		/** - return type: boolean
			* - param query: string
			* 	A string containing an SQL query.
			*
			* Executes the given query.
			**/
		function query($query)
		{//_9_5_1_5e60209_1149262227593_647954_950
			DBQuery::connect();

			$start = microtime();
			$this->result = mysql_query($query);
			$end = microtime();

			if(!$this->result) {
				DBQuery::error($query ."\n" . mysql_error());
				return false;
			}
			else {
				DBQuery::dump("+ Query: ".$query);
				DBQuery::dump("+ Time: (".($end - $start)." s)");
				return true;
			}
		}//_9_5_1_5e60209_1149262227593_647954_950

		/** - return type: resource
			*
			* Returns the result from the last run query.
			**/
		function getQueryResult()
		{//_9_5_1_5e60209_1149262698031_180661_957
			return $this->result;
		}//_9_5_1_5e60209_1149262698031_180661_957

		/** - return type: void
			*
			* Connects to the database provided by the Config object.
			**/
		function connect()
		{//_9_5_1_5e60209_1149262698031_497175_959
			if(!$this->connected) {
				DBQuery::dump("+ Connecting to database ".Config::Value("database"));
				if(!mysql_pconnect("192.168.0.23:8033", Config::Value("dbuser"), Config::Value("dbpass"))) {
					DBQuery::error(mysql_error());
				}
				$this->connected = mysql_select_db(Config::Value("database"));
				if(!$this->connected) {
					DBQuery::error(mysql_error());
				}
			}
		}//_9_5_1_5e60209_1149262698031_497175_959

		/** - return type: array
			*
			* This method fetches a result row as an associative array. It
			* returns 'false' when there are on more rows.
			**/
		function fetch()
		{//_9_5_1_5e60209_1149262698031_415614_961
			if($this->result == null || $this->result === false) {
				return array();
			}
			else {
				return mysql_fetch_assoc($this->result);
			}
		}//_9_5_1_5e60209_1149262698031_415614_961

		/** - return type: array
			*
			* This method returns a row as an indexed array or 'false' if no rows are left.
			**/
		function fetchIndex()
		{//_9_5_1_5e60209_1149262976640_252906_1167
			if($this->result == null || $this->result === false) {
				return array();
			}
			else {
				return mysql_fetch_row($this->result);
			}
		}//_9_5_1_5e60209_1149262976640_252906_1167

		/** - return type: integer
			*
			* Returns the number of rows in the last query result.
			**/
		function numRows()
		{//_9_5_1_5e60209_1149267623812_224375_1190
			if ($this->result == null || $this->result === false) {
				return 0;
			}
			else {
				return mysql_num_rows($this->result);
			}
		}//_9_5_1_5e60209_1149267623812_224375_1190

		/** - return type: integer
			*
			* This method returns the ID of the last inserted record or 'false' if there
			* isn't any.
			**/
		function getInsertID()
		{//_9_5_1_5e60209_1149262976640_686009_1169
			if($this->result != null && $this->result !== false) {
				return mysql_insert_id();
			}
			else {
				return false;
			}
		}//_9_5_1_5e60209_1149262976640_686009_1169

		/** - return type: void
			* - param message: string
			* 	The message to be written.
			* - param force: boolean
			* 	If set to 'true', forces the message to be printed.
			*
			* Status message dumper.
			**/
		function dump($message, $force = false)
		{//_9_5_1_5e60209_1149263381640_360377_1178
			if(isset($_GET['verbose']) || $force) {
				if(isset($_GET['visible']) || $force){
					echo "\n<pre style=\"padding: 0; margin: 0\">".$message."</pre>";
				}
				else {
					echo "\n<!--".$message."//-->";
				}
			}
		}//_9_5_1_5e60209_1149263381640_360377_1178

	}

?>
