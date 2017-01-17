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
        return $this
            ->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabledForChannel(ChannelInterface $channel)
    {
        $queryBuilder = $this
            ->createQueryBuilder('o')
            ->where('o.enabled = true')
        ;

        $queryBuilder
            ->innerJoin('o.channels', 'channel')
            ->andWhere($queryBuilder->expr()->eq('channel', ':channel'))
            ->setParameter('channel', $channel)
        ;

        return $queryBuilder
            ->orderBy('o.position')
            ->getQuery()
            ->getResult()
        ;
    }
}
