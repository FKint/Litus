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

namespace CudiBundle\Component\Controller;

use CommonBundle\Component\Util\AcademicYear,
    CudiBundle\Entity\Stock\Period;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    /**
     * Returns the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getAcademicYearEntity()
    {
        $date = null;
        if (null !== $this->getParam('academicyear')) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }

        return AcademicYear::getOrganizationYear($this->getEntityManager(), $date);
    }

    /**
     * Returns the active stock period.
     *
     * @return Period|null
     */
    protected function getActiveStockPeriodEntity()
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        if (!($period instanceof Period)) {
            $this->flashMessenger()->error(
                'Error',
                'There is no active stock period!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_period',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $period->setEntityManager($this->getEntityManager());

        return $period;
    }
}
