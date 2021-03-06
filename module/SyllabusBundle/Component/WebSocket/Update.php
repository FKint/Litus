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

namespace SyllabusBundle\Component\WebSocket;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\WebSocket\User,
    DateTime,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\XMLParser\Study as StudyParser,
    Zend\Mail\Transport\TransportInterface;

/**
 * This is the server to handle all requests by the websocket protocol for the Queue.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Update extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TransportInterface
     */
    private $mailTransport;

    /**
     * @var string
     */
    private $status = 'done';

    /**
     * @var StudyParser
     */
    private $parser;

    /**
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     */
    public function __construct(EntityManager $entityManager, TransportInterface $mailTransport)
    {
        parent::__construct(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.update_socket_file')
        );

        $this->entityManager = $entityManager;
        $this->mailTransport = $mailTransport;
        $this->parser = new StudyParser($this->entityManager, $this->mailTransport, array($this, 'callback'));
    }

    /**
     * Parse received text
     *
     * @param User   $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
        $command = json_decode($data);

        $key = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_key');

        if (!isset($command->key) || $command->key != $key) {
            $this->removeUser($user);
            $now = new DateTime();
            echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid key' . PHP_EOL;

            return;
        }

        if ('development' != getenv('APPLICATION_ENV')) {
            if (!isset($command->authSession)) {
                $this->removeUser($user);
                $now = new DateTime();
                echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session' . PHP_EOL;

                return;
            }

            $authSession = $this->entityManager
                ->getRepository('CommonBundle\Entity\User\Session')
                ->findOneById($command->authSession);

            $allowed = false;
            if ($authSession) {
                $acl = new Acl($this->entityManager);

                foreach ($authSession->getPerson()->getRoles() as $role) {
                    if (
                        $role->isAllowed(
                            $acl, 'syllabus_admin_update', 'updateNow'
                        )
                    ) {
                        $allowed = true;
                    }
                }
            }

            if (null == $authSession || !$allowed) {
                $this->removeUser($user);
                $now = new DateTime();
                echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session' . PHP_EOL;

                return;
            }
        }

        $this->addAuthenticated($user->getSocket());

        if ($command->command == 'update' && 'done' == $this->status) {
            $this->entityManager->clear();
            $this->status = 'updating';
            $this->parser->update();
            $this->callback('done');
            $this->status = 'done';
        }
    }

    /**
     * Parse received binary
     *
     * @param User   $user
     * @param string $data
     */
    protected function gotBin(User $user, $data)
    {
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param User $user
     */
    protected function onConnect(User $user)
    {
    }

    /**
     * @param string      $type
     * @param null|string $extra
     */
    public function callback($type, $extra = null)
    {
        if ('development' == getenv('APPLICATION_ENV')) {
            echo 'Callback: ' . $type . ' (' . $extra . ')' . PHP_EOL;
        }

        $this->sendTextToAll(
            json_encode(
                (object) array(
                    'status' => (object) array(
                        'type' => $type,
                        'extra' => substr(trim($extra), 0, 74),
                    ),
                )
            )
        );
    }
}
