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
        $GLOBALS['syslog']->enter();
        $GLOBALS['syslog']->debug("Creating:".$resource." at:".$destination,2);
        $GLOBALS['syslog']->debug("Record:".var_export($record, true),4);
        $contents = $this->getResource($resource);
        $contents = $this->interpolate($record, $contents);
        FsManager::filePutContents($destination,$contents);
        $GLOBALS['syslog']->debug("Code generated for:".$destination."\n".$contents,4);
        $GLOBALS['syslog']->finish();
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
        $source=$GLOBALS['syscfg']->cpbdir."/resources/".$resource;
                        $GLOBALS['syslog']->debug("Reading resource from:".$source, 2);
        $content = FsManager::fileGetContents($source);
                        $GLOBALS['syslog']->debug("Resource Read:".$resource, 4);
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
        $GLOBALS['syslog']->debug("ExecSql:".$query,1);
        $row = null;
        if ($result)
                @$row = mysqli_fetch_array($result);
        mysqli_close($conn);
        return $row;
    }
}
?>
