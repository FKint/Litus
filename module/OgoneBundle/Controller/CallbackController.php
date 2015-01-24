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

namespace OgoneController\Controller;

use CommonBundle\Component\Controller\ActionController;
use OgoneBundle\Component\ParameterTransformer\ParameterTransformer;
use OgoneBundle\Component\LitusOgone\Configuration;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class CallbackController extends ActionController
{
    public function postPaymentAction()
    {

        $config = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config'');
        $configuration = new Configuration($config);
        $paramTrans = new ParameterTransformer($configuration);

        $get = $this->getRequest()->getQuery();
        $postPaymentInformation = $paramTrans->handleParametersPostPayment($get);

        //$postPaymentInformation->;
        
        
    }
}
