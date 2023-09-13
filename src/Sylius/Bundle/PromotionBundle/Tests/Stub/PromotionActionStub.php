<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Tests\Stub;

use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionAction;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

#[AsPromotionAction(type: 'test', label: 'Test', priority: 10)]
final class PromotionActionStub implements PromotionActionCommandInterface
{
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        return true;
    }

    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void
    {
    }
}
