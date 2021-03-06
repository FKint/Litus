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

namespace CommonBundle\Component\WebSocket;

/**
 * This is the user who is connected to the websocket.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class User
{
    /**
     * @var resource
     */
    private $socket;

    /**
     * @var boolean
     */
    private $handshaked = false;

    /**
     * @var Frame
     */
    private $buffer;

    /**
     * @var array
     */
    private $extraData;

    /**
     * @param mixed $socket
     */
    public function __construct($socket)
    {
        $this->socket = $socket;
        $this->extraData = array();
    }

    /**
     * @return resource
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @return boolean
     */
    public function hasHandshaked()
    {
        return $this->handshaked;
    }

    /**
     * Do the handshake
     *
     * @param string $data
     */
    public function doHandshake($data)
    {
        if ($this->hasHandshaked()) {
            return;
        }

        $requestHeaders = array();

        foreach (explode("\r\n", $data) as $line) {
            @list($k, $v) = explode(':', $line, 2);
            $requestHeaders[$k] = ltrim($v);
        }

        if (empty($requestHeaders['Sec-WebSocket-Key'])) {
            return;
        }

        $key = base64_encode(sha1($requestHeaders['Sec-WebSocket-Key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

        $response = "HTTP/1.1 101 Switching Protocols\r\n"
            . "Upgrade: WebSocket\r\n"
            . "Connection: Upgrade\r\n"
            . 'Sec-WebSocket-Accept: ' . $key . "\r\n"
            . "\r\n";

        if ($this->write($response)) {
            $this->handshaked = true;
        }
    }

    /**
     * Write data to the user
     *
     * @param string $data
     */
    public function write($data)
    {
        $len = strlen($data);
        for ($written = 0; $written < $len; $written += $fwrite) {
            $fwrite = fwrite($this->socket, substr($data, $written));
            if ($fwrite === false || $fwrite === 0) {
                return $written;
            }
        }

        return $written;
    }

    /**
     * Create the buffer for fragmented frames
     *
     * @param Frame $frame
     */
    public function createBuffer(Frame $frame)
    {
        $this->buffer = $frame;
    }

    /**
     * Append data to the buffer for fragmented frames
     *
     * @param Frame $frame
     */
    public function appendBuffer(Frame $frame)
    {
        $this->buffer->appendData($frame->getData());
    }

    /**
     * Clear the buffer of this user
     */
    public function clearBuffer()
    {
        $this->buffer = null;
    }

    /**
     * Return the complete message of the user
     *
     * @return Frame|null
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function setExtraData($key, $value)
    {
        $this->extraData[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getExtraData($key)
    {
        return isset($this->extraData[$key]) ? $this->extraData[$key] : null;
    }

    /**
     * @param string $key
     *
     * @return self
     */
    public function removeExtraData($key)
    {
        unset($this->extraData[$key]);

        return $this;
    }
}
