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

namespace MailBundle\Document\Message;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * This entity stores an attachment from an e-mail message.
 *
 * @ODM\Document(
 *     collection="mailbundle_messages_attachments",
 *     repositoryClass="MailBundle\Repository\Message\Attachment"
 * )
 */
class Attachment
{
    /**
     * @var integer The ID of this attachment
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var string The filename of this attachment
     *
     * @ODM\Field(type="string")
     */
    private $filename;

    /**
     * @var string The attachment's content type
     *
     * @ODM\Field(name="content_type", type="string")
     */
    private $contentType;

    /**
     * @var string The attachment's binary data
     *
     * @ODM\Field(type="bin")
     */
    private $data;

    /**
     */
    public function __construct($filename, $contentType, $data)
    {
        $this->filename =  $filename;
        $this->contentType = $contentType;
        $this->data = $data;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
