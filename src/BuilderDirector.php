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
     * Initializes the application it implies:
     * 
     * - Creating the database
     * - Writing configurations files
     * - Adding a Setup module
     * - Adding a Security modules
     * 
     * @param array $config configuration array
     */
    function inzapp(array $config)
    {
    	$config->syslog->enter();
        $base_dir_raw = $config->cmdlne->command->options['basedir'];				
        $base_dir = FsManager::normalizePath($base_dir_raw);
        $config->syslog->debug("New App base_dir:".$base_dir,1);
        $appnme = $config->cmdlne->command->args['appnme'];		
        $appcrt = new InzApp($config, $base_dir, $appnme);	    
        $appcrt->build();
        $config->syslog->finish();		
    }
	
    /**
     * Deletes an application and delete the associated database.
     *
     * @param array $config configuration array
     */
    function delapp(array $config)
    {
    	$config->syslog->enter();
        $base_dir_raw = $config->cmdlne->command->options['basedir'];				
        $base_dir = FsManager::normalizePath($base_dir_raw);
        $appnme = $config->cmdlne->command->args['appnme'];		
        $appcrt = new DelApp($base_dir, $appnme);	    
        $appcrt->build();
        $config->syslog->finish();		
    }
	
    /**
     * The default action when called without arguments, it reads the DefFile
     * and generates any needed changes in the source files.
     * 
     * @param array $config configuration array
     */
    function rungen(array $config)
    {
    	$config->syslog->enter();
    	$base_dir_raw = './';
    	$def_file = 'DefFile';
    	if (isset($config->cmdlne->command->options['basedir']))
        {
            $base_dir_raw = $config->cmdlne->command->options['basedir'];
        }
    	if (isset($config->cmdlne->command->options['deffile']))
        {
            $def_file = $config->cmdlne->command->options['deffile'];
        }

        $base_dir = FsManager::normalizePath($base_dir_raw);
		 
        $dict = new DataDictionary($def_file);
        $builder = new RunGen($base_dir, $dict);
        $builder->build();
        $config->syslog->finish();		
    }
    
    /**
     * Dumps the Data Dictionary built after analizing the DefFile.
     * 
     * @param type $config configuration array
     */
    function dumpdd($config)
    {
        $config->syslog->enter();
        $def_file = "./DefFile";
        if (isset($config->cmdlne->options['deffile']))
        {
             $def_file = $config->cmdlne->options['deffile']; 
        }
        $dict = new DataDictionary($def_file, $config);
        $dict->dumpDictionary();
        $config->syslog->finish();	
    }		
}
?>
