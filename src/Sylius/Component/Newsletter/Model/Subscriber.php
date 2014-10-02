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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Default subscriber representation.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class Subscriber implements SubscriberInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var Collection/SubscriptionListInterface[]
     */
    protected $subscriptionLists;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subscriptionLists = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriptionList(SubscriptionListInterface $subscriptionList)
    {
        if ($this->hasSubscriptionList($subscriptionList)) {
            return $this;
        }
        $subscriptionList->addSubscriber($this);
        $this->subscriptionLists->add($subscriptionList);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriptionList(SubscriptionListInterface $subscriptionList)
    {
        if ($this->hasSubscriptionList($subscriptionList)) {
            $subscriptionList->removeSubscriber($this);
            $this->subscriptionLists->removeElement($subscriptionList);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSubscriptionList(SubscriptionListInterface $subscriptionList)
    {
        return $this->subscriptionLists->contains($subscriptionList);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriptionLists()
    {
        return $this->subscriptionLists;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
