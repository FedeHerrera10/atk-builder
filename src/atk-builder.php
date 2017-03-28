<?php
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';


use atkbuilder\Config;
use atkbuilder\BuilderDirector;
use atkbuilder\SysLogger;

error_reporting(E_ERROR | E_PARSE);


$xmlfile = dirname(__FILE__) . 
			DIRECTORY_SEPARATOR .
			'..'.
			DIRECTORY_SEPARATOR .
                        'resources'
			. DIRECTORY_SEPARATOR .
			'cmdlne'
			. DIRECTORY_SEPARATOR .
			'atk-builder_cmdlne.xml';

$cmdLineParser = PEAR2\Console\CommandLine::fromXmlFile($xmlfile);

try 
{
	$cmdlne = $cmdLineParser->parse();
}
catch (Exception $exc)
{
    $cmdLineParser->displayError($exc->getMessage());
    exit(1);
}

$cfg = new Config($cmdlne);
$cfg->logger =  new SysLogger($cmdlne->options['verbose']);
$cfg->cpbdir=dirname(dirname(__FILE__));


try 
{
    $BuilderDirector = new BuilderDirector();
    $cmd = $cmdlne->command_name;
    $command = $cmd == NULL ? 'rungen': $cmd;
    $BuilderDirector->$command($cfg);
}
catch (Exception $exc)
{
    print($exc->getMessage()."\n");
    exit(1);
}
exit(0);
?>
