<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

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
     * @var int
     */
    protected $quantity;

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
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
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
}
