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

use Doctrine\Common\Collections\Collection;

/**
 * Interface for the model representing a subscription list.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface SubscriptionListInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @param SubscriberInterface $subscriber
     */
    public function addSubscriber(SubscriberInterface $subscriber);

    /**
     * @param SubscriberInterface $subscriber
     */
    public function removeSubscriber(SubscriberInterface $subscriber);

    /**
     * @param SubscriberInterface $subscriber
     */
    public function hasSubscriber(SubscriberInterface $subscriber);

    /**
     * @return Collection/SubscriberInterface[]
     */
    public function getSubscribers();
} 