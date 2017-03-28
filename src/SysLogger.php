<?php
namespace atkbuilder;

use PEAR2\Console\CommandLine\Exception;

define("LOG_DEBUG" 	,		4);

/**
 * A class for logging 
 * 
 * @author Santiago Ottonello <sanotto@gmail.com>
 */
class SysLogger
{
    /**
     * The current log level
     * @var int
     */
    private $loglvl;
    /**
     * Create a logger.
     * 
     * @param int $loglvl The message level that should be logged
     */
    public function __construct(int $loglvl)
    {
            $this->loglvl=$loglvl;		
    }
    /**
     * Log a message.
     * 
     * @param string $message The message to log
     * @param int $level The level
     */
    public function log(string $message="", int $level=0)
    {
        $prefix="";
        if ($level >= LOG_DEBUG)
        {
            $bt = debug_backtrace();
            $prefix .= $prefix."[".$bt[1]['file']."#".$bt[1]['function']."@".$bt[0]['line']."] ";	
        } 
        $msg=$prefix.$message."\n";
        if ($level <= $this->loglvl)
        {
            print($msg);
        }
    }
    /**
     * Log a debug message
     * 
     * @param string $message The debug message.
     * @param int $level The level.
     */
    public function debug(string $message="", int $level=0)
    {
        $prefix="";
        if ($level >= LOG_DEBUG)
        {
            $bt = debug_backtrace();
            $prefix .= $prefix."[".$bt[1]['file']."#".$bt[1]['function']."@".$bt[0]['line']."] ";	
        } 
        $msg=$prefix.$message."\n";
        if ($level <= $this->loglvl)
        {
            print($msg);
        }
    }
    /**
     * Logs the entering into a function
     * 
     * @param string $message
     */
    public function enter(string $message="")
    {
        $prefix="";

        if ($this->loglvl >= LOG_DEBUG)
        {
            $bt = debug_backtrace();
            $file = basename(isset($bt[0]['file']) ? $bt[0]['file']:"?");
            $function = isset($bt[1]['function']) ? $bt[1]['function']:'';
            $prefix .= $prefix."[".$file."#".$function."@".$bt[0]['line']."] Entering Method";	
            $msg=$prefix.$message."\n";
            print $msg;
        }
    }
    /**
     * Logs the function finalization
     * 
     * @param string $message
     */
    public function finish(string $message="")
    {
        $prefix="";
        if ($this->loglvl >= LOG_DEBUG)
        {
            $bt = debug_backtrace();
            $file =basename(isset($bt[0]['file']) ? $bt[0]['file']:"?");
            $function = isset($bt[1]['function']) ? $bt[1]['function']:'';
            $prefix .= $prefix."[".$file."#".$function."@".$bt[0]['line']."] Exiting Method";	
            $msg=$prefix.$message."\n";
            print $msg;
        }
    }
    /**
     * Throw an fatal exception.
     * 
     * @param type $message
     * @throws Exception
     */
    public function abort($message)
    {
        throw new Exception($message);
    }
}
?>
