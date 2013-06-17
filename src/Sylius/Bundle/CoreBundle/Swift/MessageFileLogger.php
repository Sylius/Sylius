<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Swift;

use Swift_Events_SendListener;
use Swift_Events_SendEvent;

class MessageFileLogger implements Swift_Events_SendListener
{
    private $filename;

    /**
     * Constructor
     *
     * @param string $filename The file where the logged messages will be stored.
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Returns the logged messages.
     *
     * @return array[Swift_Message]
     */
    public function getMessages()
    {
        return $this->read();
    }

    /**
     * Clears the logged messages.
     */
    public function clear()
    {
        $this->write(array());
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $messages = $this->read();
        $messages[] = clone $evt->getMessage();

        $this->write($messages);
    }

    /**
     * {@inheritdoc}
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
    }

    private function read()
    {
        if (!file_exists($this->filename)) {
            return array();
        }

        return (array) unserialize(file_get_contents($this->filename));
    }

    private function write(array $messages)
    {
        file_put_contents($this->filename, serialize($messages));
    }
}
