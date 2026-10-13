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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\ItemInterface;

interface MenuBuilderInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function createMenu(array $options): ItemInterface;
}
