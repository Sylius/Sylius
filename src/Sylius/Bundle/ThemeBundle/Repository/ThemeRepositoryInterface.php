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

namespace Sylius\Bundle\ThemeBundle\Repository;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

interface ThemeRepositoryInterface
{
    /**
     * @return array|ThemeInterface[]
     */
    public function findAll(): array;

    public function findOneByName(string $name): ?ThemeInterface;

    public function findOneByTitle(string $title): ?ThemeInterface;
}
