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

namespace PromBundle\Controller\Registration;

use PromBundle\Entity\Bus\Passenger,
	Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Mathijs Cuppens
 */

class IndexController extends \PromBundle\Component\Controller\RegistrationController
{
	public function registrationAction()
    {
    	$createForm = $this->getForm('prom_registration_create');
    	$manageForm = $this->getForm('prom_registration_manage');

    	if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['create']))
            {
	            $createForm->setData($formData);

	            if ($createForm->isValid()) {
	                $createFormData = $createForm->getData();

	                $codeExist = $this->getEntityManager()
	            		->getRepository('PromBundle\Entity\Bus\ReservationCode')
	            		->codeExist($createFormData['create']['ticket_code']);

	            	$code = $this->getEntityManager()
			        		->getRepository('PromBundle\Entity\Bus\ReservationCode')
			        		->getRegistrationCodeByCode($createFormData['create']['ticket_code']);

	            	$passenger = $this->getEntityManager()
	            		->getRepository('PromBundle\Entity\Bus\Passenger')
	            		->findPassengerByCodeQuery($code)->getResult();

	                if ($codeExist && !isset($passenger[0]))
	                {
		        		$this->redirect()->toRoute(
		                    'prom_registration_index',
		                    array(
		                        'action' => 'create',
		                        'code' => $createFormData['create']['ticket_code'],
		                    )
		                );
	        		} else {
	        			return new ViewModel(
				    		array(
				    			'createForm' => $createForm,
				    			'manageForm' => $manageForm,
				    			'status'	 => 'fail_code',
				    		)
				    	);
	        		}
	        	}
	        }
	        elseif (isset($formData['manage']))
	        {
	        	$manageForm->setData($formData);

	        	if ($manageForm->isValid()) {
	                $manageFormData = $manageForm->getData();

	                $codeExist = $this->getEntityManager()
	            		->getRepository('PromBundle\Entity\Bus\ReservationCode')
	            		->codeExist($manageFormData['manage']['ticket_code']);

	            	if ($codeExist) {
		            	$code = $this->getEntityManager()
			        		->getRepository('PromBundle\Entity\Bus\ReservationCode')
			        		->getRegistrationCodeByCode($manageFormData['manage']['ticket_code']);

		            	$passenger = $this->getEntityManager()
		            		->getRepository('PromBundle\Entity\Bus\Passenger')
		            		->findPassengerByCodeQuery($code)->getResult()[0];

		                $correctEmail = $manageFormData['manage']['email'] == $passenger->getEmail();

		                if ($correctEmail)
		                {
		                	//$this->getRequest()->getSession()->set('code', $passenger->getCode()->getCode());

			        		$this->redirect()->toRoute(
			                    'prom_registration_index',
			                    array(
			                        'action' => 'manage',
			                        'code'	 => $passenger->getCode()->getCode(),
			                    )
			                );
		        		} else {
		        			return new ViewModel(
					    		array(
					    			'createForm' => $createForm,
					    			'manageForm' => $manageForm,
					    			'status'	 => 'fail_combination',
					    		)
					    	);
		        		}
		        	} else {
		        		return new ViewModel(
					    	array(
					    		'createForm' => $createForm,
					    		'manageForm' => $manageForm,
					    		'status'	 => 'fail_code',
					    	)
					    );
		        	}
	        	}
	        }
        }

    	return new ViewModel(
    		array(
    			'createForm' => $createForm,
    			'manageForm' => $manageForm,
    		)
    	);
    }

    public function createAction()
    {
    	$addForm = $this->getForm('prom_registration_add');
		$addForm->setData(array(
			'ticket_code' => $this->getParam('code'),
		));

    	if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData))
            {
	            $addForm->setData($formData);

	            if ($addForm->isValid()) {

		            $firstBus = $this->getEntityManager()
		        		->getRepository('PromBundle\Entity\Bus')
		        		->findOneById($formData['first_bus']);

		        	$secondBus = $this->getEntityManager()
		        		->getRepository('PromBundle\Entity\Bus')
		        		->findOneById($formData['second_bus']);

		        	$passenger = $this->getEntityManager()
		        		->getRepository('PromBundle\Entity\Bus\Passenger')
		        		->findOneByEmail($formData['email']);

		        	$code = $this->getEntityManager()
		        		->getRepository('PromBundle\Entity\Bus\ReservationCode')
		        		->getRegistrationCodeByCode($this->getParam('code'));

		        	if (!isset($passenger))
		        	{

			            if (isset($firstBus) && isset($secondBus))
			            {
			            	$newPassenger = new Passenger($formData['first_name'], $formData['last_name'], $formData['email'], $code,$firstBus,$secondBus);
			            }

			            elseif (isset($firstBus))
			            {
			            	$newPassenger = new Passenger($formData['first_name'], $formData['last_name'], $formData['email'],  $code, $firstBus, null);
			        	}

			        	elseif (isset($secondBus))
			            {
			            	$newPassenger = new Passenger($formData['first_name'], $formData['last_name'], $formData['email'],  $code, null, $secondBus);
			        	}

			        	$this->getEntityManager()->persist($newPassenger);
		                $this->getEntityManager()->flush();

		                $this->redirect()->toRoute(
			                    'prom_registration_index',
			                    array(
			                        'status' => 'success_registration',
			                    )
			                );
		            } else {
		            	return new ViewModel(
				    		array(
				    			'addForm' => $addForm,
				    			'status' => 'invalid_email',
				    		)
				    	);
		            }
	            }
	        }
    	}

	    return new ViewModel(
	    	array(
	    		'addForm' => $addForm,
	    	)
	    );
    }

    public function manageAction()
    {
    	$editForm = $this->getForm('prom_registration_edit');

    	$code = $this->getEntityManager()
		    ->getRepository('PromBundle\Entity\Bus\ReservationCode')
		    ->getRegistrationCodeByCode($this->getParam('code'));

    	$passenger = $this->getEntityManager()
	        ->getRepository('PromBundle\Entity\Bus\Passenger')
	        ->findPassengerByCodeQuery($code)->getResult()[0];

	    $firstBus = $passenger->getFirstBus();
	    $secondBus = $passenger->getSecondBus();
	    $firstBusId = (isset($firstBus)? $firstBus->getId(): 0);
	    $secondBusId = (isset($secondBus)? $secondBus->getId(): 0);

    	$editForm->setData(array(
			'ticket_code' => $passenger->getCode()->getCode(),
			'email' 	  => $passenger->getEmail(),
			'first_name'  => $passenger->getFirstName(),
			'last_name'	  => $passenger->getLastName(),
			'first_bus'   => $firstBusId,
			'second_bus'  => $secondBusId,
		));

		if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData))
            {
	            $editForm->setData($formData);

	            if ($editForm->isValid()) {

		            $firstBus = $this->getEntityManager()
		        		->getRepository('PromBundle\Entity\Bus')
		        		->findOneById($formData['first_bus']);

		        	$secondBus = $this->getEntityManager()
		        		->getRepository('PromBundle\Entity\Bus')
		        		->findOneById($formData['second_bus']);

		        	if ((($firstBus->getTotalSeats() - $firstBus->getReservedSeats()) > 0) && (($secondBus->getTotalSeats() - $secondBus->getReservedSeats()) > 0))
		        	{
			        	$code = $this->getEntityManager()
			        		->getRepository('PromBundle\Entity\Bus\ReservationCode')
			        		->getRegistrationCodeByCode($this->getParam('code'));

						$passenger = $this->getEntityManager()
			        		->getRepository('PromBundle\Entity\Bus\Passenger')
			        		->findOneByCode($code);

			        	$passenger->setFirstBus($firstBus);
			        	$passenger->setSecondBus($secondBus);

			        	$this->getEntityManager()->persist($passenger);
		                $this->getEntityManager()->flush();

		                $this->redirect()->toRoute(
			                    'prom_registration_index',
			                    array(
			                        'status' => 'success_manage',
			                    )
			                );
		        	} else {
		        		return new ViewModel( array(
    						'editForm' => $editForm,
    						'status'   => 'no_seat_left',
    						)
    					);
		        	}
		        }
		    }
		}

    	return new ViewModel(
    		array(
    			'editForm' => $editForm,
    		)
    	);
    }
}