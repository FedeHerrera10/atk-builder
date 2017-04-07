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
    public $config;
    /**
     * Creates a new application with composer using atk-skeleton.
     * Then call inzapp method to intilize this application to be used
     * with atk-builder
     * 
     * @param array $config The configuration array.
     * @param string $basedir The base directory.
     * @param string $appnme The application id.
     */
    public function __construct(Config $config,  string $basedir, string $appnme)
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
    public function Run()
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

        $this->extractFramework();
        
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
        $output = array();
        $return_var = 0;
        exec("php --ri mysqli",$output, $return_var);
        if ($return_var != 0)
        {
            throw new Exception("MysqlI extension required but not installed");
        }
       $output = array();
        $return_var = 0;
        exec("php composer.phar",$output,$return_var);
        if ($return_var != 0)
        {
            $output = array();
            $return_var = 0;
            exec("composer",$output,$return_var);
            if ($return_var != 0)
            {
                throw new Exception("composer.phar required but not found");
            }
            $this->composer="composer";
        }
        else
        {
            $this->composer="php composer.phar";
        }
    }

    private function extractFramework()
    {               
        $this->config->syslog->enter();
        $command =  $this->composer.
                                "  create-project sintattica/atk-skeleton ".
                                $this->basedir. 
                                DIRECTORY_SEPARATOR .
                                $this->appnme;      
        
        $output = array();
        $return_var = 0 ;
        exec($command,$output, $return_var);
        if($return_var !=0)
        {
            throw new Exception("Could'nt create project from composer:"+$command);
        }
        
        $initializator = new InzApp($this->config,  $this->basedir, $this->appnme);
        $initializator->Run();
        
        $this->config->syslog->finish();
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
}
?>