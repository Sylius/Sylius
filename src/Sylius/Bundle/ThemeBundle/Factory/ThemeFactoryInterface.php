<?php

namespace Sylius\Bundle\ThemeBundle\Factory;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeFactoryInterface
{
    /**
     * @param array $themeData
     *
     * @return ThemeInterface
     */
    public function createFromArray(array $themeData);
}