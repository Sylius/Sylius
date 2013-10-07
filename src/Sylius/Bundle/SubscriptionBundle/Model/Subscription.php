<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Subscription entity
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class Subscription implements SubscriptionInterface, TimestampableInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $items;

    /**
     * @var \DateTime
     */
    protected $scheduledDate;

    /**
     * @var \DateTime
     */
    protected $processedDate;

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
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduledDate()
    {
        return $this->scheduledDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduledDate(\DateTime $date)
    {
        $this->scheduledDate = $date;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedDate()
    {
        return $this->processedDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setProcessedDate(\DateTime $date)
    {
        $this->processedDate = $date;

        return $this;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(SubscriptionItemInterface $item)
    {
        if (!$this->items->contains($item)) {
            $item->setSubscription($this);
            $this->items->add($item);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(SubscriptionItemInterface $item)
    {
        if ($this->items->contains($item)) {
            $item->setSubscription(null);
            $this->items->removeElement($item);
        }

        return $this;
    }
}