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

namespace Sylius\Component\Order;

interface OrderTransitions
{
    public const GRAPH = 'sylius_order';

    public const TRANSITION_CREATE = 'create';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_FULFILL = 'fulfill';
}
