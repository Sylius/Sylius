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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessor as BasePromotionProcessor;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * Process all active promotions.
 *
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PromotionProcessor extends BasePromotionProcessor
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param PromotionRepositoryInterface         $repository
     * @param PromotionEligibilityCheckerInterface $checker
     * @param PromotionApplicatorInterface         $applicator
     * @param ChannelContextInterface              $channelContext
     */
    public function __construct(PromotionRepositoryInterface $repository, PromotionEligibilityCheckerInterface $checker, PromotionApplicatorInterface $applicator, ChannelContextInterface $channelContext)
    {
        parent::__construct($repository, $checker, $applicator);
        $this->channelContext = $channelContext;
    }

    /**
     * @return Collection
     */
    protected function getActivePromotions()
    {
        if (null === $this->promotions) {
            $channel = $this->channelContext->getChannel();
            $this->promotions = $this->repository->findActiveByChannel($channel);
        }

        return $this->promotions;
    }
}
