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

use CommonBundle\Entity\General\Config;
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        $config = new Config('key', 'value');
        $this->assertEquals('key', $config->getKey());
        $this->assertEquals('value', $config->getValue());
        $this->assertNull($config->getDescription());
        $this->assertFalse($config->isPublished());

        $config->setDescription('blah');
        $this->assertEquals('key', $config->getKey());
        $this->assertEquals('value', $config->getValue());
        $this->assertEquals('blah', $config->getDescription());
        $this->assertFalse($config->isPublished());

        $config->setPublished(true);
        $this->assertEquals('key', $config->getKey());
        $this->assertEquals('value', $config->getValue());
        $this->assertEquals('blah', $config->getDescription());
        $this->assertTrue($config->isPublished());
    }
}
