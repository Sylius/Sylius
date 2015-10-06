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

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\TransactionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

abstract class AbstractProvision implements AffiliateProvisionInterface
{
    protected $currency = 'EUR';
    protected $transactionRepository;

    public function __construct(RepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @param AffiliateInterface $affiliate
     *
     * @return TransactionInterface
     */
    protected function createTransaction(AffiliateInterface $affiliate)
    {
        /** @var $transaction TransactionInterface */
        $transaction = $this->transactionRepository->createNew();
        $transaction->setAffiliate($affiliate);
        $transaction->setCurrency($this->currency);

        $affiliate->addTransaction($transaction);

        return $transaction;
    }
}
