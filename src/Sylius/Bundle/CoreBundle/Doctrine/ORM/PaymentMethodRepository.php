<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\PaymentBundle\Doctrine\ORM\PaymentMethodRepository as BasePaymentMethodRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;

class PaymentMethodRepository extends BasePaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder($locale)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.gatewayConfig', 'gatewayConfig')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabledForChannel(ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.enabled = true')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult()
        ;
    }
}
