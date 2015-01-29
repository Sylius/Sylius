<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\OrderProcessing;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessor as BasePromotionProcessor;

/**
 * Process all active promotions.
 *
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PromotionProcessor extends BasePromotionProcessor
{
    protected $channelContext;

    public function __construct(PromotionRepositoryInterface $repository, PromotionEligibilityCheckerInterface $checker, PromotionApplicatorInterface $applicator, ChannelContextInterface $channelContext)
    {
        parent::__construct($repository, $checker, $applicator);
        $this->channelContext = $channelContext;
    }

    protected function getActivePromotions()
    {
        if (null === $this->promotions) {
            $channel = $this->channelContext->getChannel();
            $this->promotions = $this->repository->findActiveByChannel($channel);
        }

        return $this->promotions;
    }
}
