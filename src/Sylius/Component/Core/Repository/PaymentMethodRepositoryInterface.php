<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface as BasePaymentMethodRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface PaymentMethodRepositoryInterface extends BasePaymentMethodRepositoryInterface
{

    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();
        
    /**
     * @param ChannelInterface $channel
     * 
     * @return array
     */
    public function findEnabledForChannel(ChannelInterface $channel);
}
