<?php

namespace atkbuilder;

use PEAR2\Console\CommandLine\Exception;

class NewApp extends AbstractCodeCreator
{
    /**
     * Array containing configuration values
     * @access private
     * @var array
     */
    private $config;
    /**
     * Initializes an application to be maintained with atk-builder.
     * 
     * @param array $config The configuration array.
     * @param string $basedir The base directory.
     * @param string $appnme The application id.
     */
    public function __construct(array $config,  string $basedir, string $appnme)
    {
        $this->config = $config;
        $this->config->syslog->enter();
        $this->basedir=$basedir;		
        $this->appnme=$appnme;
        $this->full_basedir = FsManager::normalizePath($this->basedir.DIRECTORY_SEPARATOR.$appnme);
        $this->dbname = trim($this->config->cmdlne->command->options['dbname']);
        $this->dbname = trim($this->dbname) == "" ? $this->appnme:$this->dbname;
        $this->dbhost = trim($this->config->cmdlne->command->options['dbhost']);
        $this->dbuser = trim($this->config->cmdlne->command->options['dbuser']);
        $this->dbpass = trim($this->config->cmdlne->command->options['dbpass']);
        $this->appass = trim($this->config->cmdlne->command->options['appass']);
        if(trim($this->appass)=="")
        {
            $this->appass="demo";
        }
        $this->config->syslog->finish();
    }
	
    /**
     * Init the application-
     */
    public function initialize()
    {
        $this->config->syslog->enter();

        try
        { 
            $this->checkPreRequisites();
            FsManager::assertFileExists($this->basedir);
            FsManager::assertFileNotExists($this->full_basedir);
        } catch(Exception $e)
        {
                throw new Exception($e->getMessage());
        }
        //if ($this->dbpass == null)
        //	throw new Exception("This option requires a database user and password provide it with -u and -p. -u defaults to root");
        FsManager::ensureFolderExists($this->basedir);
        $this->assertDatabaseNew();
        $this->extractFramework();
        $this->createDefFile();
        $this->updateConfig();
        $this->runCodeGen();
        $this->updateDependencies();
        echo "\n";
        echo "App creation completed\n";
        echo "First: Update dependicies with:\n";
        echo "php ./composer update:\n";
        echo "then check your app with:\n\n";
        echo "cd ".$this->full_basedir."\n";
        echo "php -S localhost:8080 -t web\n\n";
        echo "Open your browser at http://localhost:8080 and acces the app with:\n";
        echo "user: administrator\n";
        echo "pass: ".$this->appass."\n";
        echo "\n";
        echo "After logging in Please execute the Setup Menú option to Set the database up\n";
        $this->config->syslog->finish();	
    }
	
    /**
     * Check if the mysql extension is intalled.
     * 
     * @throws Exception if mysqli not installed.
     */
    private function checkPreRequisites()
    {
        $command="php --ri mysqli";
        $output=array();
        $return_var=0;
        exec($command,$output,$return_var);
        if ($return_var != 0)
        {
            throw new Exception("MysqlI extension required but not installed");
        }
    }

    private function extractFramework()
    {
        $this->config->syslog->enter();
        $source=$this->config->cpbdir.DIRECTORY_SEPARATOR."resources".DIRECTORY_SEPARATOR;
        $from=$source.'newproject';
        $to= $this->full_basedir;
        FsManager::copyr($from,$to);
        //Create symbolic link into web/bundles to atk -> ../../vendor/sintattica/atk/src/Resources/public
        $from = '../..//vendor/sintattica/atk/src/Resources/public';
        $to = $this->full_basedir.'/web/bundles/atk';
        FsManager::symLink($from, $to);
        $this->config->syslog->finish();
    }	
    /**
     *  Updates the configuration file config/atk.php
     *  - update the app identifier
     * 
     */
    private function updateConfig()
    {
        $config_file = $this->full_basedir.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'atk.php';
        $config_contents = FsManager::fileGetContents($config_file);
        $start_offset = strpos($config_contents, "'identifier' => '");
        $end_offset = strpos($config_contents, "',", $start_offset);
        $config_contents =	substr($config_contents, 0, $start_offset).
        "'identifier' => '". $this->appnme .
        substr($config_contents,$end_offset);
        FsManager::filePutContents($config_file, $config_contents);
    }
    /**
     * Run code generation
     */
    private function runCodeGen()
    {
        chdir($this->full_basedir);
        try 
        {
                $BuilderDirector = new BuilderDirector();
                $BuilderDirector->rungen('');
        }
        catch (Exception $exc)
        {
            print($exc->getMessage()."\n");
            exit(1);
        }	
    }
    /**
     * Create the database only if it not exists.
     * 
     * @throws Exception if database allready exists.
     */
    private function assertDatabaseNew()
    {
        $dbname=$this->dbname;
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '${dbname}'";
        $row = $this->execSQL($query);
        if ($row != false)
        {
            throw new Exception("Database:".$dbname." allready exists");
        }
        $query = "CREATE DATABASE ${dbname};";
        $row = $this->execSQL($query);
        $query = "GRANT ALL ON ${dbname}.* TO `${dbname}`@`localhost` identified by '${dbname}';";
        $row = $this->execSQL($query);
    }
	
    /**
     * Create the DefFile
     */
    private function createDefFile()
    {					
        $this->config->syslog->enter();
        $record = array();
        $record["appnme"]=$this->appnme;
        $record["dbnme"]=$this->appnme;
        $record["dbusr"]=$this->appnme;
        $record["dbpas"]=$this->appnme;	
        $from = 'templates'.DIRECTORY_SEPARATOR.'DefFile';
        $to = $this->full_basedir.DIRECTORY_SEPARATOR.'DefFile';
        $this->createFromTemplate($from, $record, $to);	
        $this->config->syslog->finish();						
    }
    /**
     *  Update dependencies
     */
    private function updateDependencies()
    {
        $this->config->syslog->enter();
        //chdir($this->full_basedir);
        exec("php ./composer update");
        $this->config->syslog->finish();
    }
}
?>
