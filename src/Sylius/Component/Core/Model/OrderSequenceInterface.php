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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderSequenceInterface as BaseOrderSequenceInterface;
use Sylius\Resource\Model\VersionedInterface;

interface OrderSequenceInterface extends BaseOrderSequenceInterface, VersionedInterface
{
}
