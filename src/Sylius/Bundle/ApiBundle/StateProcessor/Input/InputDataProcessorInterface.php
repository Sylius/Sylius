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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

/** @experimental */
interface InputDataProcessorInterface extends ProcessorInterface
{
    public function supports(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): bool;
}
