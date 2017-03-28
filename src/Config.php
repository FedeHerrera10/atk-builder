<?php

namespace atkbuilder;

use PEAR2\Console\CommandLine\Exception;
/**
 * A class to manage the configuration of the code generator.
 * 
 * @author Santiago Ottonello <sanotto@gmail.com>
 */
class Config 
{
 
    /*
     * An array to contain all the configuration values
     * 
     * @access private
     * @var array
     */
    private $config;
	
    /**
     * Configuration managemente class.
     * 
     * @param array $cmdlne The command line arguments.
     */
    function __construct(array $cmdlne)
    {
        $this->config['cwd']=getcwd();
        $this->config['basedir']=getcwd();
        $this->config['cmdlne']=$cmdlne;
    }
    /**
     * Magic method to return the value of $name from the configuration array.
     * 
     * @param type $name The key to be retrieved.
     * @return type The configuration value
     * @throws Exception On name not found.
     */
    public function __get($name) 
    {
        if (array_key_exists($name, $this->config)) 
        {
            return $this->config[$name];
        }
        $trace = debug_backtrace();
        throw new Exception('Uknown config entry:'.$name." in". $trace[0]['file']." at line:".$trace[0]['line']);
    }
    /**
     * Magic method to set the value of $name into the configuration array.
     * 
     * @param type $name The key to be asigned.
     * @param type $value The value to be asigned.
     * @return type The configuration value
     * @throws Exception On name not found.
     */
    public function __set($name, $value) 
    {
        $this->config[$name] = $value;
    }	
}

?>