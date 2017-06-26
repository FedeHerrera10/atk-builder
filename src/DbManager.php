<?php 

namespace atkbuilder;

if (!defined('MYSQLI_READ_DEFAULT_GROUP'))
{
/**OC
 * <p>
 * Read options from the named group from my.cnf
 * or the file specified with MYSQLI_READ_DEFAULT_FILE
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_READ_DEFAULT_GROUP', 5);

/**
 * <p>
 * Read options from the named option file instead of from my.cnf
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_READ_DEFAULT_FILE', 4);

/**
 * <p>
 * Connect timeout in seconds
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_OPT_CONNECT_TIMEOUT', 0);

/**
 * <p>
 * Enables command LOAD LOCAL INFILE
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_OPT_LOCAL_INFILE', 8);

/**
 * <p>
 * Command to execute when connecting to MySQL server. Will automatically be re-executed when reconnecting.
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_INIT_COMMAND', 3);

/**
 * <p>
 * Use SSL (encrypted protocol). This option should not be set by application programs; 
 * it is set internally in the MySQL client library
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CLIENT_SSL', 2048);

/**
 * <p>
 * Use compression protocol
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CLIENT_COMPRESS', 32);

/**
 * <p>
 * Allow interactive_timeout seconds
 * (instead of wait_timeout seconds) of inactivity before
 * closing the connection. The client's session
 * wait_timeout variable will be set to
 * the value of the session interactive_timeout variable. 
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CLIENT_INTERACTIVE', 1024);

/**
 * <p>
 * Allow spaces after function names. Makes all functions names reserved words. 
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CLIENT_IGNORE_SPACE', 256);

/**
 * <p>
 * Don't allow the db_name.tbl_name.col_name syntax.
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CLIENT_NO_SCHEMA', 16);
define ('MYSQLI_CLIENT_FOUND_ROWS', 2);

/**
 * <p>
 * For using buffered resultsets
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_STORE_RESULT', 0);

/**
 * <p>
 * For using unbuffered resultsets
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_USE_RESULT', 1);

/**
 * <p>
 * Columns are returned into the array having the fieldname as the array index.
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_ASSOC', 1);

/**
 * <p>
 * Columns are returned into the array having an enumerated index.
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_NUM', 2);

/**
 * <p>
 * Columns are returned into the array having both a numerical index and the fieldname as the associative index. 
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_BOTH', 3);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_STMT_ATTR_UPDATE_MAX_LENGTH', 0);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_STMT_ATTR_CURSOR_TYPE', 1);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CURSOR_TYPE_NO_CURSOR', 0);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CURSOR_TYPE_READ_ONLY', 1);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CURSOR_TYPE_FOR_UPDATE', 2);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_CURSOR_TYPE_SCROLLABLE', 4);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_STMT_ATTR_PREFETCH_ROWS', 2);

/**
 * <p>
 * Indicates that a field is defined as NOT NULL
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_NOT_NULL_FLAG', 1);

/**
 * <p>
 * Field is part of a primary index
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_PRI_KEY_FLAG', 2);

/**
 * <p>
 * Field is part of a unique index.
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_UNIQUE_KEY_FLAG', 4);

/**
 * <p>
 * Field is part of an index.
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_MULTIPLE_KEY_FLAG', 8);

/**
 * <p>
 * Field is defined as BLOB
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_BLOB_FLAG', 16);

/**
 * <p>
 * Field is defined as UNSIGNED
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_UNSIGNED_FLAG', 32);

/**
 * <p>
 * Field is defined as ZEROFILL
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_ZEROFILL_FLAG', 64);

/**
 * <p>
 * Field is defined as AUTO_INCREMENT
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_AUTO_INCREMENT_FLAG', 512);

/**
 * <p>
 * Field is defined as TIMESTAMP
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TIMESTAMP_FLAG', 1024);

/**
 * <p>
 * Field is defined as SET
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_SET_FLAG', 2048);

/**
 * <p>
 * Field is defined as NUMERIC
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_NUM_FLAG', 32768);

/**
 * <p>
 * Field is part of an multi-index
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_PART_KEY_FLAG', 16384);

/**
 * <p>
 * Field is part of GROUP BY
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_GROUP_FLAG', 32768);

/**
 * <p>
 * Field is defined as DECIMAL
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_DECIMAL', 0);

/**
 * <p>
 * Field is defined as TINYINT
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_TINY', 1);

/**
 * <p>
 * Field is defined as SMALLINT
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_SHORT', 2);

/**
 * <p>
 * Field is defined as INT
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_LONG', 3);

/**
 * <p>
 * Field is defined as FLOAT
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_FLOAT', 4);

/**
 * <p>
 * Field is defined as DOUBLE
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_DOUBLE', 5);

/**
 * <p>
 * Field is defined as DEFAULT NULL
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_NULL', 6);

/**
 * <p>
 * Field is defined as TIMESTAMP
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_TIMESTAMP', 7);

/**
 * <p>
 * Field is defined as BIGINT
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_LONGLONG', 8);

/**
 * <p>
 * Field is defined as MEDIUMINT
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_INT24', 9);

/**
 * <p>
 * Field is defined as DATE
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_DATE', 10);

/**
 * <p>
 * Field is defined as TIME
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_TIME', 11);

/**
 * <p>
 * Field is defined as DATETIME
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_DATETIME', 12);

/**
 * <p>
 * Field is defined as YEAR
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_YEAR', 13);

/**
 * <p>
 * Field is defined as DATE
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_NEWDATE', 14);

/**
 * <p>
 * Field is defined as ENUM
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_ENUM', 247);

/**
 * <p>
 * Field is defined as SET
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_SET', 248);

/**
 * <p>
 * Field is defined as TINYBLOB
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_TINY_BLOB', 249);

/**
 * <p>
 * Field is defined as MEDIUMBLOB
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_MEDIUM_BLOB', 250);

/**
 * <p>
 * Field is defined as LONGBLOB
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_LONG_BLOB', 251);

/**
 * <p>
 * Field is defined as BLOB
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_BLOB', 252);

/**
 * <p>
 * Field is defined as VARCHAR
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_VAR_STRING', 253);

/**
 * <p>
 * Field is defined as STRING
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_STRING', 254);

/**
 * <p>
 * Field is defined as CHAR
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_CHAR', 1);

/**
 * <p>
 * Field is defined as INTERVAL
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_INTERVAL', 247);

/**
 * <p>
 * Field is defined as GEOMETRY
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_GEOMETRY', 255);

/**
 * <p>
 * Precision math DECIMAL or NUMERIC field (MySQL 5.0.3 and up)
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_NEWDECIMAL', 246);

/**
 * <p>
 * Field is defined as BIT (MySQL 5.0.3 and up)
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_TYPE_BIT', 16);

/**
 * <p>
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_SET_CHARSET_NAME', 7);
define ('MYSQLI_RPL_MASTER', 0);
define ('MYSQLI_RPL_SLAVE', 1);
define ('MYSQLI_RPL_ADMIN', 2);

/**
 * <p>
 * No more data available for bind variable
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_NO_DATA', 100);

/**
 * <p>
 * Data truncation occurred. Available since PHP 5.1.0 and MySQL 5.0.5.
 * </p>
 * @link http://www.php.net/manual/en/mysqli.constants.php
 */
define ('MYSQLI_DATA_TRUNCATED', 101);
define ('MYSQLI_REPORT_INDEX', 4);
define ('MYSQLI_REPORT_ERROR', 1);
define ('MYSQLI_REPORT_STRICT', 2);
define ('MYSQLI_REPORT_ALL', 255);
define ('MYSQLI_REPORT_OFF', 0);
}

class DbManager
{
	public function __construct($dd, $config)
	{
		$this->config=$config;
        	$this->config->syslog->enter();
		$this->dd=$dd;
		$database=$this->dd['db']['dbname'];
		$user=$this->dd['db']['user'];
		$password=$this->dd['db']['password'];
		$host=$this->dd['db']['host'];
		$port=$this->dd['db']['port'];
		$charset=$this->dd['db']['charset'];
		$this->doConnect($host, $user, $password, $database, $port, $charset);	
        	$this->config->syslog->finish();
	}
 	
	function doConnect($host, $user, $password, $database, $port, $charset)
    {
		if (empty($port)) $port = NULL;
       	$this->m_link_id = mysqli_connect($host, $user, $password, $database, $port);
       	if (!$this->m_link_id)
       		$this->config->syslog->abort('Could not connect to database, reason:'.  mysql_error($this->m_link));
       	/* set character set */
       	if (!empty($charset))
			$this->_query("SET NAMES '{$charset}'", true);

       	/* set autoCommit to off */
       	mysqli_autocommit($this->m_link_id, FALSE);
       	return $this->m_link_id;
    }
    
	protected function _query($query, $isSystemQuery)
   	{
    	return @mysqli_query($this->m_link_id, $query);
   	}
   	
    function metadata($table, $full=false)
   	{
   	
       	/* The tablename may also contain a schema. If so we check for it. */
		if (strpos($table, ".") !== false)
		{
			list($dbname, $tablename) = explode(".", $table);         
			/* get meta data */
			$id = @$this->_query("SELECT * FROM `{$dbname}`.`{$tablename}` LIMIT 1", true);
       	}
       	else
       	{
			/* get meta data */
			$id = $this->_query("SELECT * FROM `{$table}` LIMIT 1", true);
       	}
	
		// table type
		//$tableType = $this->_getTableType($table);
		if (!$id)
			$this->config->syslog->abort('Could not connect to database');

		
		$result = array();

		while ($finfo = mysqli_fetch_field($id))
		{
			$i=$finfo->name;
			$result[$i]["table"]      = $finfo->table;
	        $result[$i]["name"]       = $finfo->name;
	        $result[$i]["type"]       = $finfo->type;
	        $result[$i]["gentype"]    = $this->getGenericType($finfo->type);
	        $result[$i]["len"]        = $finfo->length;
	        $result[$i]["flags"]      = 0;

	        if ($result[$i]["gentype"] == "decimal")
	        {
	          $result[$i]["len"] -= $finfo->decimals + 1;
	          $result[$i]["len"] .= "," . $finfo->decimals;
	        }

	        if($finfo->flags & MYSQLI_PRI_KEY_FLAG) $result[$i]["flags"]|= MF_PRIMARY;
	        if($finfo->flags & MYSQLI_UNIQUE_KEY_FLAG) $result[$i]["flags"]|= MF_UNIQUE;
	        if($finfo->flags & MYSQLI_NOT_NULL_FLAG ) $result[$i]["flags"]|= MF_NOT_NULL;
	        if($finfo->flags & MYSQLI_AUTO_INCREMENT_FLAG) $result[$i]["flags"]|= MF_AUTO_INCREMENT;

         	if ($full)
           		$result["meta"][$result[$i]["name"]] = $i;
      	}

      	if ($full)
          $result["num_fields"] = $i;

        mysqli_free_result($id);
        return $result;
   	}
   
	function tablesForPrefix($prefix)
   	{
		$id = @$this->_query("show tables like '{$prefix}%'; ", true);
		if (!$id)
			$this->config->syslog->abort('Could not connect to database');
		$i  = 0;
		$result = array();
		while ($tinfo = mysqli_fetch_array($id))
		{
			$result[$i]=$tinfo[0];
			$i++;
      	}
        mysqli_free_result($id);
        return $result;
   	}
   	
	function _getTableType($table)
   	{		
     	$id = $this->_query("SHOW TABLE STATUS LIKE '" .$table ."'", true);
     	$status = @mysqli_fetch_array($id, MYSQLI_ASSOC|atkconfig("mysqlfetchmode"));
     	$result = $status != NULL && isset($status['Engine']) ? $status['Engine'] : NULL;
     	return $result;
	}
	   
	function getGenericType($type)
	{
		$type = strtolower($type);
	      switch($type)
	      {
	        case MYSQLI_TYPE_TINY:
	        case MYSQLI_TYPE_SHORT:
	        case MYSQLI_TYPE_LONG:
	        case MYSQLI_TYPE_LONGLONG:
	        case MYSQLI_TYPE_INT24:
	          return "number";
	        case MYSQLI_TYPE_DECIMAL:
	        case MYSQLI_TYPE_NEWDECIMAL:
	        case MYSQLI_TYPE_FLOAT:
	        case MYSQLI_TYPE_DOUBLE:
	          return "decimal";        
	        case MYSQLI_TYPE_VAR_STRING:
	        case MYSQLI_TYPE_STRING:
	          return "string";          
	        case MYSQLI_TYPE_DATE:
	          return "date";
	        case MYSQLI_TYPE_TINY_BLOB:
	        case MYSQLI_TYPE_MEDIUM_BLOB:
	        case MYSQLI_TYPE_LONG_BLOB:
	        case MYSQLI_TYPE_BLOB:
	          return "text";
	        case MYSQLI_TYPE_TIME:
	          return "time";
	        case MYSQLI_TYPE_TIMESTAMP:
	        case MYSQLI_TYPE_DATETIME:
	          return "datetime";      
	        case MYSQLI_TYPE_YEAR:
	        case MYSQLI_TYPE_NEWDATE:
	        case MYSQLI_TYPE_ENUM:
	        case MYSQLI_TYPE_SET:         
	        case MYSQLI_TYPE_GEOMETRY:
	          return ""; // NOT SUPPORTED FIELD TYPES 
	      }
	      return ""; // in case we have an unsupported type.      
    }   	
}
?>
