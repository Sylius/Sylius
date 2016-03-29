<?php

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Affiliate\Repository\AffiliateGoalRepositoryInterface as BaseAffiliateGoalRepositoryInterface;
use Sylius\Component\Affiliate\Model\AffiliateGoalInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
interface AffiliateGoalRepositoryInterface extends BaseAffiliateGoalRepositoryInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return AffiliateGoalInterface[]
     */
    public function findActiveByChannel(ChannelInterface $channel);
}
