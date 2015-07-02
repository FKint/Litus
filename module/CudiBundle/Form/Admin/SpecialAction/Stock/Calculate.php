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

namespace CudiBundle\Form\Admin\SpecialAction\Stock;

/**
 * Recalculate stock and orders
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Calculate extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'select',
            'name'     => 'supplier',
            'label'    => 'Supplier',
            'required' => true,
            'attributes' => array(
                'options' => $this->_getSuppliers(),
            ),
        ));

        $this->add(array(
            'type'     => 'select',
            'name'     => 'semester',
            'label'    => 'Semester',
            'required' => true,
            'attributes' => array(
                'options' => $this->_getSemesters(),
            ),
        ));

        $this->addSubmit('Calculate', 'action');
    }

    private function _getSemesters()
    {
        return array('1' => 1, '2' => '2');
    }

    private function _getSuppliers()
    {
        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        $suppliersArray = array(
            array(
                'label' => '',
                'value' => '',
            ),
        );

        foreach ($suppliers as $supplier) {
            $suppliersArray[] = array(
                'label' => $supplier->getName(),
                'value' => $supplier->getId(),
            );
        }

        return $suppliersArray;
    }
}
