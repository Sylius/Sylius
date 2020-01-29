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

namespace Sylius\Component\Core\Dashboard;

class SalesSummary
{
    /**
     * @var int[]
     * @psalm-var array<string, int>
     */
    private $monthsSaleMap;

    public function __construct(array $monthsSaleMap)
    {
        $this->monthsSaleMap = $monthsSaleMap;
    }

    public function getMonths(): array
    {
        return array_keys($this->monthsSaleMap);
    }

    public function getSales(): array
    {
        return array_values($this->monthsSaleMap);
    }
}
