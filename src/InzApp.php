<?php

namespace atkbuilder;

use PEAR2\Console\CommandLine\Exception;

class InzApp extends AbstractCodeCreator
{
    /**
     * Array containing configuration values
     * @access private
     * @var array
     */
    public $config;
    /**
     * Initializes an application to be maintained with atk-builder.
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
        $this->assertDatabaseNew();                
        $this->createDefFile();   
        $this->addSetupModule();
        $this->addAtkBuilderNode();
        $this->deleteSkeletonModules();
        $this->deleteSkeletonProvidedSqlFile();
        $this->updateParametersDev();
        $this->updateAtkDotPhp();
        $this->runCodeGen();
        $this->updateDependencies();
        $this->sayWeAreReady();
        $this->config->syslog->finish();	
    }
    /**
     *  Copies the Setup Module from resources/modules to app's src/Modules
     */
    private function addSetupModule()
    {
        $source=$this->config->cpbdir.DIRECTORY_SEPARATOR.
                        "resources";
        $from =$source.DIRECTORY_SEPARATOR.
                    'modules'. \DIRECTORY_SEPARATOR .'Setup';        
        $to =   $this->full_basedir.DIRECTORY_SEPARATOR.
                    'src'.DIRECTORY_SEPARATOR.
                    'Modules'.DIRECTORY_SEPARATOR.
                    'Setup';        
        FsManager::copyr($from, $to) ;
    }
    /**
     *  Copies the AtkBuilderNode class from resources/classes to app's src/Modules
     */
    private function addAtkBuilderNode()
    {
        $source=$this->config->cpbdir.DIRECTORY_SEPARATOR.
                        "resources";
        $from =$source.DIRECTORY_SEPARATOR.
                    'classes'. \DIRECTORY_SEPARATOR .'AtkBuilderNode.php';        
        $to =   $this->full_basedir.DIRECTORY_SEPARATOR.
                    'src'.DIRECTORY_SEPARATOR.
                    'Modules';        
        FsManager::copy($from, $to) ;
    }
    /**
     *  Deletes the two atk-skeleton provided module App y Auth
     */
     private function deleteSkeletonModules()
     {
         $folder=$this->full_basedir.DIRECTORY_SEPARATOR.
                    'src'.DIRECTORY_SEPARATOR.
                    'Modules'.DIRECTORY_SEPARATOR.
                    'App';        
         FsManager::rmdir($folder);
         $folder=$this->full_basedir.DIRECTORY_SEPARATOR.
                    'src'.DIRECTORY_SEPARATOR.
                    'Modules'.DIRECTORY_SEPARATOR.
                    'Auth';        
         FsManager::rmdir($folder);
     }
     /**
      * Deletes the providede atk-skeleton.sql because it is not needed anymore
      * and it will not be used
      */
     private function deleteSkeletonProvidedSqlFile()
     {
        $file=$this->full_basedir.DIRECTORY_SEPARATOR.
                    'atk-skeleton.sql';
            FsManager::unlink($file);
     }
    /**
     * Prints the finalization message
     */
    private function sayWeAreReady()
    {
        echo "\n";
        echo "App initialization completed\n";
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
    }
    /**
     *  Updates the configuration file parameters.dev.php
     *  - update the app identifier.
     *  - Update db connection data
     *  - Update application master password
     * 
     */
    private function updateParametersDev()
    {
        $password= password_hash($this->appass, PASSWORD_DEFAULT);        
        
        $config_file = $this->full_basedir.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'parameters.dev.php';
        
        $config_contents = FsManager::fileGetContents($config_file);
        $config_contents = $this->replace_entry($config_contents, "identifier", $this->appnme);
        $config_contents = $this->replace_entry($config_contents, "db", $this->dbname);
        $config_contents = $this->replace_entry($config_contents, "host", $this->dbhost);
        $config_contents = $this->replace_entry($config_contents, "user", $this->dbuser);
        $config_contents = $this->replace_entry($config_contents, "password", $this->dbpass);                        
        $config_contents = $this->replace_entry($config_contents, "administratorpassword", $password);  
        
        FsManager::filePutContents($config_file, $config_contents);
    }
    /**
     *  Updates the configuration file atk.php
     *  - update the app identifier.
     *  - Update db connection data
     *  - Update application master password 
     */
    private function updateAtkDotPhp()
    {
        $config_file = $this->full_basedir.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'atk.php';
        $config_contents = FsManager::fileGetContents($config_file);
        $config_contents = $this->replace_entry($config_contents, "securityscheme", "group");
        $config_contents = $this->replace_entry($config_contents, "auth_userfk", "user_id");
        $config_contents = $this->replace_entry($config_contents, "auth_usernode", "Security.Users");
        $config_contents = $this->replace_entry($config_contents, "auth_usertable", "security_users");
        $config_contents = $this->replace_entry($config_contents, "auth_userfield", "username");
        $config_contents = $this->replace_entry($config_contents, "auth_emailfield", "email");
        $config_contents = $this->replace_entry($config_contents, "auth_accountdisablefield", "disabled");
        $config_contents = $this->replace_entry($config_contents, "auth_leveltable", "security_users_groups");
        $config_contents = $this->replace_entry($config_contents, "auth_levelfield", "group_id");
        $config_contents = $this->replace_entry($config_contents, "auth_accesstable", "security_accessrights");
        $config_contents = $this->replace_entry($config_contents, "auth_administratorfield", "is_admin");
        
        $start_offset = strpos($config_contents, "];");
        if ($start_offset !== 0) 
        {            
            $config_contents =substr($config_contents, 0, $start_offset).
            "'setup_allowed_ips' => '127.0.0.1:127.0.0.0'\n];" ;
        }
        FsManager::filePutContents($config_file, $config_contents);
    }
    /**
     * 
     * @param string $contents Contents to search and replace
     * @param \atkbuilder\string $tag The tag
     * @param string $value The value
     */
    private function replace_entry(string $config_contents, string $tag, string $value): string
    {
        $needle="'".$tag."' => '";
        
        $start_offset = strpos($config_contents, $needle);
        if ($start_offset !== 0) 
        {            
            $end_offset = strpos($config_contents, "',", $start_offset);
            $config_contents =substr($config_contents, 0, $start_offset).
            "'".$tag."' => '". $value .
            substr($config_contents,$end_offset);            
        }        
        return $config_contents;
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
                $BuilderDirector->rungen($this->config);
        }
        catch (Exception $exc)
        {
            print("Exception:".$exc->getMessage()."\n");
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
        $this->CreateDataBase($dbname);
        $query = "GRANT ALL ON ${dbname}.* TO `${dbname}`@`localhost` identified by '${dbname}';";
        $row = $this->execSQL($query);
        
         if ($row != false)
        {
            throw new Exception("Database:".$dbname." could not be created");
        }
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
        $from = 'templates'. \DIRECTORY_SEPARATOR .'DefFile';        
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
