<?php
 
namespace NewsBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
	protected function initConfig()
	{
		$this->installConfig(
	        array(
				/*array(
        			'key'         => 'search_max_results',
        			'value'       => '30',
        			'description' => 'The maximum number of search results shown',
        		),*/
			)
		);
	}
	
	protected function initAcl()
	{
	    $this->installAclStructure(
	        array(
	            'newsBundle' => array(
	                'common_news' => array(
	                	'overview', 'view'
	                ),
	                'admin_news' => array(
	                    'add', 'delete', 'edit', 'manage'
	                ),
	            ),
	        )
	    );
	    
	    $this->installRoles(
	        array(
    	        'guest' => array(
    	            'parent_roles' => array(),
    	            'actions' => array(
    	                'common_news' => array(
    	                	'overview', 'view'
    	                ),
    	            ),
    	        ),
    	    )
    	);
	}
}