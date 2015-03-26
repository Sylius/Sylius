<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Contact\Model;

use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Interface for the model representing a contact request.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface RequestInterface extends CustomerAwareInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     */
    public function setMessage($message);

    /**
     * @return TopicInterface
     */
    public function getTopic();

    /**
     * @param TopicInterface $topic
     */
    public function setTopic(TopicInterface $topic = null);
}
