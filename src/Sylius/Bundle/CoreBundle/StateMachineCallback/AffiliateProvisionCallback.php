<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\StateMachineCallback;

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\Transaction;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class AffiliateProvisionCallback
{
    private $affiliateRepository;

    public function __construct(RepositoryInterface $affiliateRepository)
    {
        $this->affiliateRepository = $affiliateRepository;
    }

    public function setAffiliateProvision(OrderInterface $order)
    {
        if (null !== $referralCode = $order->getReferralCode()) {
            /** @var $affiliate AffiliateInterface */
            if (null !== $affiliate = $this->affiliateRepository->findOneBy(array('referralCode' => $referralCode))) {
                $transaction = new Transaction();
                $transaction->setAmount($this->getAmount($order, $affiliate));

                $affiliate->addTransaction($transaction);
            }
        }
    }

    private function getAmount(OrderInterface $order, AffiliateInterface $affiliate)
    {
        $amount = $affiliate->getProvisionAmount();
        if (AffiliateInterface::PROVISION_PERCENT === $affiliate->getProvisionType()) {
            return (int) round($order->getTotal() / $amount);
        }

        if (AffiliateInterface::PROVISION_FIXED === $affiliate->getProvisionType()) {
            return $amount;
        }

        return $amount;
    }
}
