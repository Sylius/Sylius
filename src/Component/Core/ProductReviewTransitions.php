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

namespace Sylius\Component\Core;

interface ProductReviewTransitions
{
    public const GRAPH = 'sylius_product_review';

    public const TRANSITION_ACCEPT = 'accept';

    public const TRANSITION_REJECT = 'reject';
}
