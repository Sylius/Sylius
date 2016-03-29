<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Provision;

use Sylius\Component\Affiliate\Model\AffiliateGoalInterface;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\RewardInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

abstract class AbstractProvision implements AffiliateProvisionInterface
{
    protected $currency = 'EUR';
    protected $rewardFactory;
    protected $currencyContext;

    public function __construct(CurrencyContextInterface $currencyContext, FactoryInterface $rewardFactory)
    {
        $this->currencyContext = $currencyContext;
        $this->rewardFactory = $rewardFactory;
    }

    /**
     * @param AffiliateInterface $affiliate
     *
     * @return RewardInterface
     */
    protected function createReward(AffiliateInterface $affiliate, AffiliateGoalInterface $goal)
    {
        /** @var $reward RewardInterface */
        $reward = $this->rewardFactory->createNew();
        $reward->setAffiliate($affiliate);
        $reward->setCurrency($this->currencyContext->getCurrency());
        $reward->setGoal($goal);

        $affiliate->addReward($reward);

        return $reward;
    }
}
