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
namespace CommonBundle\Test\Entity\General;

use CommonBundle\Entity\General\AcademicYear;

class AcademicYearTest extends \PHPUnit_Framework_TestCase
{
    public function testAcademicYear()
    {
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', '2014-10-01 00:00:00');
        $universityStart = \DateTime::createFromFormat('Y-m-d H:i:s', '2014-10-16 00:00:00');
        $academicYear = new AcademicYear($start, $universityStart);

        //$end == First monday of the next university year.
        $end = \Datetime::createFromFormat('Y-m-d H:i:s', '2015-09-21 00:00:00');

        $this->assertEquals('1415', $academicYear->getCode(true));
        $this->assertEquals('2014-2015', $academicYear->getCode(false));
        $this->assertEquals($start, $academicYear->getStartDate());
        $this->assertEquals($universityStart, $academicYear->getUniversityStartDate());

        $this->assertEquals($end, $academicYear->getEndDate());
        $this->assertEquals($end, $academicYear->getUniversityEndDate());
    }
}
