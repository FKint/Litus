<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SportBundle\Component\WebSocket\Run;

use CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * This is the server to handle all requests by the websocket protocol for the Queue.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Queue extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param string $address The url for the websocket master socket
     * @param integer $port The port to listen on
     */
    public function __construct(EntityManager $entityManager)
    {
        $address = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_host');
        $port = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_port');

        parent::__construct($address, $port);

        $this->_entityManager = $entityManager;
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    protected function onConnect(User $user)
    {
        $this->sendQueue($user);
    }

    /**
     * Parse received text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
        $this->_entityManager->clear();

        if (strpos($data, 'action: ') === 0) {
            $this->_gotAction($user, $data);
        } elseif ($data == 'queueUpdated') {
            $this->sendQueueToAll();
        }
    }

    /**
     * Parse action text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param string $data
     */
    private function _gotAction(User $user, $data)
    {
        $action = substr($data, strlen('action: '), strpos($data, ' ', strlen('action: ')) - strlen('action: '));
        $params = trim(substr($data, strpos($data, ' ', strlen('action: ')) + 1));

        switch ($action) {
            case 'addToQueue':
                break;
        }
    }

    /**
     * Send queue to one user
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     */
    private function sendQueue(User $user)
    {
        $this->sendText($user, $this->_getJsonQueue());
    }

    /**
     * Send queue to all users
     */
    private function sendQueueToAll()
    {
        foreach($this->getUsers() as $user)
            $this->sendQueue($user);
    }

    private function _getJsonQueue()
    {
        $resultPage = @simplexml_load_file(
            $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_result_page')
        );

        $nbOfficialLaps = null;
        if (null !== $resultPage) {
            $teamId = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $nbOfficialLaps = (string) $resultPage->xpath('//team[@id=\'' . $teamId . '\']')[0]->rounds;
        }

        $nbLaps = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->countAll();

        $data = (object) array(
            'laps' => (object) array(
                'official' => $nbOfficialLaps,
                'own' => $nbLaps,
            ),
        );

        return json_encode($data);
    }
}