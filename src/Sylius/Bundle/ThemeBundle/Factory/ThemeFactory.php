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

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeFactory extends Factory implements ThemeFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNamed($name)
    {
        /** @var ThemeInterface $theme */
        $theme = $this->createNew();
        $theme->setName($name);

        return $theme;
    }
}
