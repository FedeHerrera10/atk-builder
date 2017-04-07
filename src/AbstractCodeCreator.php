<?php

namespace atkbuilder;

use PEAR2\Console\CommandLine\Exception;

/**
 * The AbstractCodeCreator class contains method for source code
 * creation.
 * This class can be inherited for specific code creators that will
 * use it's methods to create code.
 *
 * @author Santiago Ottonello <sanotto@gmail.com>
 */
abstract class AbstractCodeCreator
{
    /**
     * Creates a source file by replacing the values contained in record
     * into the specified template saving the result in destination.
     *
     * @param string $resource The template for the resource you want created.
     * @param array  $record An array containing values to be replaced in the template.
     * @param string $destination The output path for the generated resource.
     */
    protected  function createFromTemplate(string $resource, array $record, string $destination)
    {
        $this->config->syslog->enter();
        $this->config->syslog->debug("Creating:".$resource." at:".$destination,2);
        $this->config->syslog->debug("Record:".var_export($record, true),4);
        $contents_raw = $this->getResource($resource);
        $contents = $this->interpolate($record, $contents_raw);                                
        FsManager::filePutContents($destination,$contents);       
        $this->config->syslog->debug("Code generated for:".$destination."\n".$contents,4);
        $this->config->syslog->finish();
    }

    /**
     * Creates a new text replacing the tags marked with ${tag}
     * with the values contained in $record[tag].
     *
     * @param array  $record An array containing values to be replaced in 
     * the template.
     * @param string $contents The template.
     * 
     * @return string The template with the tag replaced by its values.
     */
    protected  function interpolate($record, $contents): string
    {
        foreach($record as $field => $value)
        {
                $search= '${'.trim($field).'}';
                $contents = str_ireplace($search, $value, $contents);
        }
        return $contents;
    }

    /**
     * Retrieves an resource from the resource repository.
     * 
     * @param  string $resource the resource name to be recovered
     * @return string The resource text
     */
    protected  function getResource(string $resource): string
    {
        $source=$this->config->cpbdir.DIRECTORY_SEPARATOR.
                        "resources".DIRECTORY_SEPARATOR.
                        $resource;
        $this->config->syslog->debug("Reading resource from:".$resource, 2);
        $content = FsManager::fileGetContents($source);
        $this->config->syslog->debug("Resource Read:".$resource, 4);
        return $content;
    }

    /**
     * Executes an SQL Query.
     * 
     * @param string $query The Query to be executed
     * @return array An array with result rows
     * @throws Exception
     */
    protected function execSQL(string $query): array
    {
        $conn = mysqli_connect($this->dbhost, $this->dbuser,$this->dbpass);
        if (!$conn)
        {
            throw new Exception("Could not connect to database server with the supplied parameters. (User:".$this->dbuser." ,Pass:".$this->dbpass.")");
        }
        $result = mysqli_query($query);
        $this->config->syslog->debug("ExecSql:".$query,1);
        $row = array();
        if ($result)
        {
                @$row = mysqli_fetch_array($result);
        }
        mysqli_close($conn);
        return $row;
    }
    /**
     * Creates a database
     * 
     * @param string $dbname The database name
     * @return bool  true if succesfull
     * @throws Exception
     */
    public function CreateDataBase(string $dbname):bool
    {
        $conn = new \mysqli($this->dbhost, $this->dbuser,$this->dbpass);
        if ($conn->connect_error)
        {
            throw new Exception("Could'nt connect with database");
        }
        $query = "CREATE DATABASE $dbname;";
        if($conn->query($query)==true )
        {
            return true;
        }        
        return false;
    }
}
?>
