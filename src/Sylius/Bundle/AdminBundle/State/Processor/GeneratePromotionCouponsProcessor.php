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

namespace Sylius\Bundle\AdminBundle\State\Processor;

use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProcessorInterface;

final readonly class GeneratePromotionCouponsProcessor implements ProcessorInterface
{
    public function __construct(
        private PromotionCouponGeneratorInterface $promotionCouponGenerator,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {


        dd(__METHOD__);

        return null;
    }
}
