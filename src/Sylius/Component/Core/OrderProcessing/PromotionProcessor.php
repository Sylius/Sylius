<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessor as BasePromotionProcessor;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PromotionProcessor extends BasePromotionProcessor
{
    /**
     * @param PromotionSubjectInterface $subject
     *
     * @return array
     */
    protected function getActivePromotions(PromotionSubjectInterface $subject)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        if (null === $this->promotions) {
            $channel = $subject->getChannel();
            $this->promotions = $this->repository->findActiveByChannel($channel);
        }

        return $this->promotions;
    }
}
