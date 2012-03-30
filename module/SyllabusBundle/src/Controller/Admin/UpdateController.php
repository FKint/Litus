<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace SyllabusBundle\Controller\Admin;

use SyllabusBundle\Component\XMLParser\Study as StudyParser;

/**
 * UpdateController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class UpdateController extends \CommonBundle\Component\Controller\ActionController
{

    public function indexAction()
    {
    
    }
    
    public function xmlAction()
    {
    }
    
	public function updateAction()
	{
		new StudyParser($this->getEntityManager(), simplexml_load_file('http://litus/admin/syllabus/update/xml'));
		
		return array(
			'updateReady' => true
		);
    }
}