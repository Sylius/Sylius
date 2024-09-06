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

namespace Sylius\Component\Addressing\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of ZoneInterface
 *
 * @extends FactoryInterface<T>
 */
interface ZoneFactoryInterface extends FactoryInterface
{
    public function createTyped(string $type): ZoneInterface;

    public function createWithMembers(array $membersCodes): ZoneInterface;
}
