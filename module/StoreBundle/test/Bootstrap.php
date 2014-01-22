<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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


