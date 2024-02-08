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

namespace Sylius\Component\Promotion\Model;

final class CatalogPromotionTransitions
{
    public const GRAPH = 'sylius_catalog_promotion';

    public const TRANSITION_PROCESS = 'process';

    public const TRANSITION_ACTIVATE = 'activate';

    public const TRANSITION_DEACTIVATE = 'deactivate';

    private function __construct()
    {
    }
}
