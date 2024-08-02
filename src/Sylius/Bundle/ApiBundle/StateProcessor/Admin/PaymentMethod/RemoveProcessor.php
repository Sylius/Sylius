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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\PaymentMethod;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeRemoved;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<PaymentMethodInterface> */
final readonly class RemoveProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $removeProcessor,
    ) {
    }

    /**
     * @param ProductInterface $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, PaymentMethodInterface::class);
        Assert::isInstanceOf($operation, DeleteOperationInterface::class);

        try {
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } catch (ForeignKeyConstraintViolationException) {
            throw new PaymentMethodCannotBeRemoved();
        }
    }
}
