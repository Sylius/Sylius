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

use Symfony\Component\Finder\Finder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FinderFactory implements FinderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return Finder::create();
    }
}
