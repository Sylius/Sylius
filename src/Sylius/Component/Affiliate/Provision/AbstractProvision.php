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
use Sylius\Component\Affiliate\Model\TransactionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

abstract class AbstractProvision implements AffiliateProvisionInterface
{
    protected $currency = 'EUR';
    protected $transactionFactory;
    protected $currencyContext;

    public function __construct(CurrencyContextInterface $currencyContext, FactoryInterface $transactionFactory)
    {
        $this->currencyContext = $currencyContext;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param AffiliateInterface $affiliate
     *
     * @return TransactionInterface
     */
    protected function createTransaction(AffiliateInterface $affiliate, AffiliateGoalInterface $goal)
    {
        /** @var $transaction TransactionInterface */
        $transaction = $this->transactionFactory->createNew();
        $transaction->setAffiliate($affiliate);
        $transaction->setCurrency($this->currencyContext->getCurrency());
        $transaction->setGoal($goal);

        $affiliate->addTransaction($transaction);

        return $transaction;
    }
}
