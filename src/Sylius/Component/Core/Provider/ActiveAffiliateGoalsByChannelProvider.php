<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Affiliate\Provider\ActiveAffiliateGoalsProvider;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\AffiliateGoalRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class ActiveAffiliateGoalsByChannelProvider extends ActiveAffiliateGoalsProvider
{
    /**
     * @var AffiliateGoalRepositoryInterface
     */
    protected $channelContext;

    /**
     * @param AffiliateGoalRepositoryInterface $repository
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(AffiliateGoalRepositoryInterface $repository, ChannelContextInterface $channelContext)
    {
        parent::__construct($repository);

        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateGoals($subject = null)
    {
        if ($subject instanceof OrderInterface) {
            $channel = $subject->getChannel();
        } else {
            $channel = $this->channelContext->getChannel();
        }

        return ($channel instanceof ChannelInterface)
            ? $this->repository->findActiveByChannel($channel)
            : $this->repository->findActive();
    }
}