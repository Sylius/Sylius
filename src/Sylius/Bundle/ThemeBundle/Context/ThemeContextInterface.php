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

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

interface ThemeContextInterface
{
    /**
     * Should not throw any exception if failed to get theme.
     *
     * @return ThemeInterface|null
     */
    public function getTheme(): ?ThemeInterface;
}
