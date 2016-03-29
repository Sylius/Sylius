<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Model;
use Sylius\Component\Affiliate\Model\AffiliateGoalInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Transaction implements TransactionInterface
{
    /**
     * Transaction id.
     *
     * @var int
     */
    protected $id;

    /**
     * Transaction type.
     *
     * @var int
     */
    protected $type = TransactionInterface::TYPE_EARNING;

    /**
     * Transaction amount.
     *
     * @var float
     */
    protected $amount = 0;

    /**
     * Transaction currency.
     *
     * @var string
     */
    protected $currency;

    /**
     * @var AffiliateInterface
     */
    protected $affiliate;

    /**
     * Affiliate goal that was completed
     *
     * @var AffiliateGoalInterface
     */
    protected $goal;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEarning()
    {
        return TransactionInterface::TYPE_EARNING === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isPayment()
    {
        return TransactionInterface::TYPE_PAYOUT === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return AffiliateGoalInterface
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * @param AffiliateGoalInterface $goal
     */
    public function setGoal(AffiliateGoalInterface $goal)
    {
        $this->goal = $goal;
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliate(AffiliateInterface $affiliate = null)
    {
        $this->affiliate = $affiliate;

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
