<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Filtering;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

interface FiltersCriteriaResolverInterface
{
    public function hasCriteria(Grid $grid, Parameters $parameters): bool;

    public function getCriteria(Grid $grid, Parameters $parameters): array;
}
