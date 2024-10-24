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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\Common;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Component\Core\Exception\ResourceDeleteException;

final class ResourceRemoveProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $decoratedRemoveProcessor)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        try {
            $this->decoratedRemoveProcessor->process($data, $operation, $uriVariables, $context);
        } catch (ForeignKeyConstraintViolationException) {
            $shortName = (new \ReflectionClass($data))->getShortName();
            $resourceName = strtolower(preg_replace('/(?<!^)([A-Z])/', ' $1', $shortName));

            throw new ResourceDeleteException($resourceName);
        }
    }
}
