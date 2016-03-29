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

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface RewardInterface extends AffiliateAwareInterface, TimestampableInterface
{
    const TYPE_EARNING = 1;
    const TYPE_PAYOUT  = 2;

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Is reward earning?
     *
     * @return bool
     */
    public function isEarning();

    /**
     * Is reward payment?
     *
     * @return bool
     */
    public function isPayment();

    /**
     * Set type of reward.
     *
     * @param int $type
     *
     * @return self
     */
    public function setType($type);

    /**
     * Get amount.
     *
     * @return float
     */
    public function getAmount();

    /**
     * Set amount.
     *
     * @param float $amount
     *
     * @return self
     */
    public function setAmount($amount);

    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency.
     *
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency($currency);

    /**
     * Set affiliate goal this reward applies to.
     *
     * @return self
     */
    public function setGoal(AffiliateGoalInterface $goal);

    /**
     * Returns affiliate goal this reward applies to.
     *
     * @return AffiliateGoalInterface
     */
    public function getGoal();
}
