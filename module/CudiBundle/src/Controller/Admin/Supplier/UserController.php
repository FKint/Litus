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
 
namespace CudiBundle\Controller\Admin\Supplier;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Credential,
    CudiBundle\Entity\Users\People\Supplier as SupplierPerson,
    CudiBundle\Form\Admin\Supplier\User\Add as AddForm,
    CudiBundle\Form\Admin\Supplier\User\Edit as EditForm;

/**
 * UserController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class UserController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
	{
	    if (!($supplier = $this->_getSupplier()))
	    	return;
	    	
		$paginator = $this->paginator()->createFromEntity(
		    'CudiBundle\Entity\Users\People\Supplier',
		    $this->getParam('page'),
		    array(
		        'canLogin' => true,
		        'supplier' => $supplier->getId()
		    ),
		    array(
		        'username' => 'ASC'
		    )
		);
        
        return array(
            'supplier' => $supplier,
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl()
        );
    }
    
    public function addAction()
    {
        if (!($supplier = $this->_getSupplier()))
        	return;
        
        $form = new AddForm($this->getEntityManager());
		
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $newUser = new SupplierPerson(
                    $formData['username'],
                    array(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('supplier')
                    ),
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email'],
                    $formData['phone_number'],
					$formData['sex'],
					$supplier
                );
                $newUser->activate($this->getEntityManager(), $this->getMailTransport());
                $this->getEntityManager()->persist($newUser);
                exit;
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The supplier user was successfully created!'
                    )
                );
                
                $this->redirect()->toRoute(
                	'admin_supplier_user',
                	array(
                		'action' => 'manage',
                		'id' => $supplier->getId(),
                	)
                );
                
                return;
            }
        }
                
        return array(
            'supplier' => $supplier,
        	'form' => $form,
        );
    }
    
    public function editAction()
    {
        if (!($user = $this->_getUser()))
        	return;
        			
        $form = new EditForm($this->getEntityManager(), $user);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
            if ($form->isValid($formData)) {
                $user->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['email'])
                    ->setSex($formData['sex'])
                    ->setPhoneNumber($formData['phone_number']);
                
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The supplier user was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_supplier_user',
                	array(
                		'action' => 'manage',
                		'id' => $user->getSupplier()->getId()
                	)
                );
                
                return;
            }
        }
        
        return array(
            'supplier' => $user->getSupplier(),
        	'form' => $form,
        );
    }
    
    public function deleteAction()
	{
		$this->initAjax();

		if (!($user = $this->_getUser()))
			return;

		$user->disableLogin();
		$this->getEntityManager()->flush();
        
        return array(
            'result' => (object) array("status" => "success")
        );
	}
    
    private function _getSupplier()
    {
    	if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the supplier!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_supplier',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $supplier) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No supplier with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_supplier',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $supplier;
    }
	
	private function _getUser()
	{
		if (null === $this->getParam('id')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No id was given to identify the supplier!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_supplier',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $supplier = $this->getEntityManager()
	        ->getRepository('CudiBundle\Entity\Users\People\Supplier')
	        ->findOneById($this->getParam('id'));
		
		if (null === $supplier) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No supplier with the given id was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_supplier',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $supplier;
	}
}