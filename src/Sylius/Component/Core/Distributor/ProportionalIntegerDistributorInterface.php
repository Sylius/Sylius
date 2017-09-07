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

namespace Sylius\Component\Core\Distributor;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ProportionalIntegerDistributorInterface
{
    /**
     * @param array $integers
     * @param int $amount
     *
     * @return array
     */
    public function distribute(array $integers, int $amount): array;
}
