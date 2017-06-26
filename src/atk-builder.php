<?php

$builder_dir = dirname(__FILE__). DIRECTORY_SEPARATOR;
$levels = 0;
$step_back='';
$autoload_found=false;
for ($i=0;$i<10;$i++)
{
  $step_back .= '..'.DIRECTORY_SEPARATOR;
  $autoload = $builder_dir.$step_back. 
              'vendor'.DIRECTORY_SEPARATOR.
               'autoload.php';
  if (file_exists($autoload))
  {
    require $autoload;
    $autoload_found = true;
    break;
  }
}

if(!$autoload_found) die ("Could'nt found autoload");

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
$verbose = $cmdlne->options['verbose'] ?? 0;
$cfg->syslog =  new SysLogger($verbose);
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
