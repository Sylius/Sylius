<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeAuthorFactoryInterface
{
    /**
     * @param array $data
     *
     * @return ThemeAuthor
     */
    public function createFromArray(array $data);
}
