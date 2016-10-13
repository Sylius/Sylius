<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Event;

use Sylius\Component\Mailer\Model\EmailInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class EmailSendEvent extends Event
{
    /**
     * @var mixed
     */
    protected $message;

    /**
     * @var string[]
     */
    protected $recipients;

    /**
     * @var EmailInterface
     */
    protected $email;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param mixed $message
     * @param array $recipients
     * @param EmailInterface $email
     * @param array $data
     */
    public function __construct($message, EmailInterface $email, array $data, array $recipients = [])
    {
        $this->message = $message;
        $this->email = $email;
        $this->data = $data;
        $this->recipients = $recipients;
    }

    /**
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @return EmailInterface
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
