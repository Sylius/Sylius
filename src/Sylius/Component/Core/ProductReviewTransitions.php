<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductReviewTransitions
{
    const GRAPH = 'sylius_product_review';

    const TRANSITION_ACCEPT = 'accept';
    const TRANSITION_REJECT = 'reject';
}
