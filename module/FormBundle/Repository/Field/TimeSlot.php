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

namespace FormBundle\Repository\Field;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\User\Person,
    DateTime,
    FormBundle\Entity\Node\Form;

/**
 * TimeSlot
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TimeSlot extends EntityRepository
{
    public function findOneOccupationByPersonAndTime(Person $person, DateTime $start, DateTime $end)
    {
        $query = $this->_em->createQueryBuilder();
        $timeSlots = $query->select('f.id')
            ->from('FormBundle\Entity\Field\TimeSlot', 'f')
            ->where(
                $query->expr()->orX(
                    $query->expr()->andX(
                        $query->expr()->lte('f.startDate', ':start'),
                        $query->expr()->gt('f.endDate', ':start')
                    ),
                    $query->expr()->andX(
                        $query->expr()->lt('f.startDate', ':end'),
                        $query->expr()->gte('f.endDate', ':end')
                    ),
                    $query->expr()->andX(
                        $query->expr()->gte('f.startDate', ':start'),
                        $query->expr()->lte('f.endDate', ':end')
                    )
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($timeSlots as $timeSlot) {
            $ids[] = $timeSlot['id'];
        }

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('FormBundle\Entity\Entry', 'e')
            ->innerJoin('e.field', 'f')
            ->innerJoin('e.formEntry', 'fe')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('f.id', $ids),
                    $query->expr()->eq('fe.creationPerson', ':person')
                )
            )
            ->setParameter('person', $person)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllConflictingByFormAndTimeQuery(Form $form, DateTime $start, DateTime $end)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('FormBundle\Entity\Field\TimeSlot', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->andX(
                            $query->expr()->lte('t.startDate', ':start'),
                            $query->expr()->gt('t.endDate', ':start')
                        ),
                        $query->expr()->andX(
                            $query->expr()->lt('t.startDate', ':end'),
                            $query->expr()->gte('t.endDate', ':end')
                        ),
                        $query->expr()->andX(
                            $query->expr()->gte('t.startDate', ':start'),
                            $query->expr()->lte('t.endDate', ':end')
                        )
                    ),
                    $query->expr()->eq('t.form', ':form')
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('form', $form)
            ->getQuery();

        return $resultSet;
    }

    public function findAllForReminderMailQuery(DateTime $start, DateTime $end)
    {
        $query = $this->_em->createQueryBuilder();
        $reminderDoodles = $query->select('d')
            ->from('FormBundle\Entity\Node\Form\Doodle', 'd')
            ->where(
                $query->expr()->isNotNull('d.reminderMail')
            )
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach($reminderDoodles as $doodle)
            $ids[] = $doodle->getId();

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('FormBundle\Entity\Field\TimeSlot', 't')
            ->innerJoin('t.form', 'f')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gte('t.startDate', ':start'),
                    $query->expr()->lt('t.startDate', ':end'),
                    $query->expr()->in('f.id', $ids)
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return $resultSet;
    }
}
