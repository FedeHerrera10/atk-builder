<?php

namespace atkbuilder;

use atkbuilder\AbstractCodeCreator;

class DelApp extends AbstractCodeCreator
{
    /**
     * Array containing configuration values
     * @access private
     * @var array
     */
    private $config;
    /**
     * Deletes an application
     * 
     * @param array $config The configuration array.
     * @param string $basedir The base directory.
     * @param string $appnme The application id.
     */
    public function __construct(array $config, string $basedir, string $appnme)
    {
        $this->config = $config;
        
        $this->config->syslog->enter();
        $this->basedir=$basedir;		
        $this->appnme=$appnme;
        $this->full_basedir = FsManager::normalizePath($this->basedir.$appnme);
        $this->dbname = trim($def_file = $this->config->cmdlne->command->options['dbname']);
        $this->dbname =  trim($this->dbname) == "" ? $this->appnme:$this->dbname;
        $this->dbhost = trim($def_file = $this->config->cmdlne->command->options['dbhost']);
        $this->dbuser = trim($def_file = $this->config->cmdlne->command->options['dbuser']);
        $this->dbpass = trim($def_file = $this->config->cmdlne->command->options['dbpass']);

        $this->config->syslog->finish();
    }
	
    /**
     * Builds the new application in the base_dir passed to the creator
     * using the def file passed in the creator.
     */
    public function Run()
    {
        $this->config->syslog->enter();
        try
        { 
            FsManager::assertFileExists($this->full_basedir);
        } catch(Exception $e)
        {
            print($e->getMessage());
        }
        //if ($this->dbpass == null)
        //	throw new Exception("This option requires a database user and password provide it with -u and -p. -u defaults to root");
        FsManager::rmdir($this->full_basedir);
        $this->delDatabase();

        $this->config->syslog->finish();	
    }
    /**
     * Delete the database
     */
    private function delDatabase()
    {
        $dbname=$this->dbname;
        $query = "DROP DATABASE ${dbname};";
        $row = $this->execSQL($query);
    }
}
?>