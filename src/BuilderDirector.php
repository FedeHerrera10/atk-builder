<?php
/**
 * This file is part of atk code generator
 */


namespace atkbuilder;

/**
 * Determines, instantiates and call the class needed to carry on any of the
 * builder activities
 * 
 * @author Santiago Ottonello <sanotto@gmail.com>
 */
class BuilderDirector 
{
    /**
     * Magic method called when a non existing method is called
     * 
     * @param string $method_name The name of the non existing method.
     * @param array $arguments The non existing method calling parameters.
     * @throws Exception if the method called can not be infered
     */
    public function __call(string $method_name, array $arguments) 
    {
        $GLOBALS['syslog']->enter();
        if (!method_exists($this, $method_name))
        {
            throw new Exception('Uknown command:'.$method_name);
        }
        $this->$method_name($arguments);
        $GLOBALS['syslog']->finish();
    }
	
    /**
     * Creates a new atk-builder enabled application.
     * It implies:
     * 
     * - cloning atk-skeleton via composer
     * - initializing the application with inzapp method    
     * 
     * @param array $config configuration array
     */
    function newapp(Config $config)
    {
        $config->syslog->enter();
        $base_dir_raw = $config->cmdlne->command->options['basedir'];				
        $base_dir = FsManager::normalizePath($base_dir_raw);
        $config->syslog->debug("New App base_dir:".$base_dir,1);
        $appnme = $config->cmdlne->command->args['appnme'];		
        $appcrt = new NewApp($config, $base_dir, $appnme);	    
        $appcrt->Run();
        $config->syslog->finish();		
    }
    /**
     * Initializes the application it implies:
     * 
     * - Creating the database.
     * - Create the versioning table
     * - updating configurations files
     * - Adding the Setup module
     * - Creating a Default DefFile with security nodes definitions
     *  -Running a rungen command to create de sources
     * 
     * @param array $config configuration array
     */
    function inzapp(Config $config)
    {
        $config->syslog->enter();
        $base_dir = $config->basedir;
        $config->syslog->debug("New App base_dir:".$base_dir,1);
        $appnme = $config->cmdlne->command->args['appnme'];		
        $appcrt = new InzApp($config, $base_dir, $appnme);	    
        $appcrt->Run();
        $config->syslog->finish();		
    }
	
    /**
     * Deletes an application and delete the associated database.
     *
     * @param array $config configuration array
     */
    function delapp(Config $config)
    {
        $config->syslog->enter();
        $base_dir_raw = $config->cmdlne->command->options['basedir'];				
        $base_dir = FsManager::normalizePath($base_dir_raw);
        $appnme = $config->cmdlne->command->args['appnme'];		
        $appcrt = new DelApp($base_dir, $appnme);	    
        $appcrt->Run();
        $config->syslog->finish();		
    }
	
    /**
     * The default action when called without arguments, it reads the DefFile
     * and generates any needed changes in the source files.
     * 
     * @param array $config configuration array
     */
    function rungen(Config  $config)
    {
        $config->syslog->enter();
        $def_file = $config->deffile;
        $base_dir = $config->basedir;		 
        $dict = new DataDictionary($def_file, $config);
        $builder = new RunGen($config, $base_dir, $dict);
        $builder->Run();
        $config->syslog->finish();		
    }
    
    /**
     * Dumps the Data Dictionary built after analizing the DefFile.
     * 
     * @param type $config configuration array
     */
    function dumpdd(Config $config)
    {
        $config->syslog->enter();
        $def_file = "./DefFile";
        if (isset($config->cmdlne->options['deffile']))
        {
             $def_file = $config->cmdlne->options['deffile']; 
        }
        $dict = new DataDictionary($def_file, $config);
        $dict->DumpDictionary();
        $config->syslog->finish();	
    }		
}
?>
