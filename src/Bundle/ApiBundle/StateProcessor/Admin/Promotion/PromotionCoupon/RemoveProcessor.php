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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ApiBundle\Exception\PromotionCouponCannotBeRemoved;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<PromotionCouponInterface> */
final readonly class RemoveProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $removeProcessor,
    ) {
    }

    /** @param PromotionCouponInterface $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, PromotionCouponInterface::class);
        Assert::isInstanceOf($operation, DeleteOperationInterface::class);

        try {
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } catch (ForeignKeyConstraintViolationException) {
            throw new PromotionCouponCannotBeRemoved();
        }
    }
}
