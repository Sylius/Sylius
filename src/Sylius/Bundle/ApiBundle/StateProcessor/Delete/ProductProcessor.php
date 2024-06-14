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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Delete;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ApiBundle\Exception\ProductCannotBeRemoved;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final readonly class ProductProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
    ) {
    }

    /**
     * @param ProductInterface $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, ProductInterface::class);

        if ($operation instanceof DeleteOperationInterface) {
            try {
                return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
            } catch (ForeignKeyConstraintViolationException) {
                throw new ProductCannotBeRemoved();
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
