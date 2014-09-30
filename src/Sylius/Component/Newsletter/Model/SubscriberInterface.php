<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Newsletter\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Interface for the model representing a subscriber.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface SubscriberInterface extends TimestampableInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();
}
