<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Twig;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsManagerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SettingsExtension extends \Twig_Extension
{
    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var ThemeSettingsManagerInterface
     */
    private $themeSettingsManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(ThemeContextInterface $themeContext, ThemeSettingsManagerInterface $themeSettingsManager)
    {
        $this->themeContext = $themeContext;
        $this->themeSettingsManager = $themeSettingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_theme_settings', [$this, 'getThemeSettings']),
        ];
    }

    /**
     * @return array
     */
    public function getThemeSettings()
    {
        $theme = $this->themeContext->getTheme();

        if (null === $theme) {
            return [];
        }

        return $this->themeSettingsManager->load($theme);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_theme_settings';
    }
}
