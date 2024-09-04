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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductVariant;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ApiBundle\Exception\ProductVariantCannotBeRemoved;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<ProductVariantInterface> */
final readonly class RemoveProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $removeProcessor,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, ProductVariantInterface::class);
        Assert::isInstanceOf($operation, DeleteOperationInterface::class);

        try {
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } catch (ForeignKeyConstraintViolationException) {
            throw new ProductVariantCannotBeRemoved();
        }
    }
}
