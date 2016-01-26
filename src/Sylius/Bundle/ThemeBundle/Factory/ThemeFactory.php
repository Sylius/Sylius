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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeFactory implements ThemeFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $basicThemeFactory;

    /**
     * @param FactoryInterface $basicThemeFactory
     */
    public function __construct(FactoryInterface $basicThemeFactory)
    {
        $this->basicThemeFactory = $basicThemeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $themeData)
    {
        /** @var ThemeInterface $theme */
        $theme = $this->basicThemeFactory->createNew();

        $theme->setName($themeData['name']);
        $theme->setPath($themeData['path']);

        if (isset($themeData['authors'])) {
            $theme->setAuthors($themeData['authors']);
        }

        if (isset($themeData['title'])) {
            $theme->setTitle($themeData['title']);
        }

        if (isset($themeData['description'])) {
            $theme->setDescription($themeData['description']);
        }

        if (isset($themeData['parents'])) {
            $theme->setParentsNames($themeData['parents']);
        }

        return $theme;
    }
}
