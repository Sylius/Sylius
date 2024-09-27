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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductOption;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Exception\ProductOptionValueCannotBeRemoved;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<ProductOptionInterface> */
final readonly class PersistProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, ProductOptionInterface::class);
        Assert::isInstanceOf($operation, Put::class);

        try {
            $this->processor->process($data, $operation, $uriVariables, $context);
        } catch (ResourceDeleteException) {
            throw new ProductOptionValueCannotBeRemoved();
        }
    }
}
