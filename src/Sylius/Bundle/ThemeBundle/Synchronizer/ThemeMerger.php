<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Synchronizer;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeMerger implements ThemeMergerInterface
{
    /**
     * @var HydratorInterface
     */
    private $themeHydrator;

    /**
     * @param HydratorInterface $themeHydrator
     */
    public function __construct(HydratorInterface $themeHydrator)
    {
        $this->themeHydrator = $themeHydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(ThemeInterface $existingTheme, ThemeInterface $loadedTheme)
    {
        $loadedThemeProperties = $this->themeHydrator->extract($loadedTheme);

        unset($loadedThemeProperties['id']);

        return $this->themeHydrator->hydrate($loadedThemeProperties, $existingTheme);
    }
}
