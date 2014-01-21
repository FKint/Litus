<?php

function fakbarLoader1($class_name)
{	
	$file = '../../' . str_replace('\\', '/', $class_name) . '.php';
	
	if(is_file($file))
		include $file;
};

function fakbarLoader2($class_name)
{	
	$file = str_replace('_', '/', $class_name) . '.php';
	
	foreach(explode(':', get_include_path()) as $dir)
	{
	    $loc = $dir.'/'.$file;
	    
	    if(is_file($loc))
	    {
		    include $loc;
		    return;
	    }
	}

};

spl_autoload_register('fakbarLoader1');
spl_autoload_register('fakbarLoader2');

include 'Doctrine/Common/ClassLoader.php';

$doctrineLoader = new Doctrine\Common\ClassLoader();
$doctrineLoader->register();


