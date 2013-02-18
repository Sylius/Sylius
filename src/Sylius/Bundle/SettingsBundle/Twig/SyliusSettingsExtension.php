<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Twig;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Sylius settings extension for Twig.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusSettingsExtension extends Twig_Extension
{
    /**
     * Settings manager.
     *
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * Constructor.
     *
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_settings_all' => new Twig_Function_Method($this, 'getSettings'),
            'sylius_settings_get' => new Twig_Function_Method($this, 'getSettingsParameter'),
        );
    }

    /**
     * Load settings from given namespace.
     *
     * @param string $namespace
     *
     * @return array
     */
    public function getSettings($namespace)
    {
        return $this->settingsManager->loadSettings($namespace);
    }

    /**
     * Load settings parameter for given namespace and name.
     *
     * @param string $namespace
     * @param string $name
     *
     * @return mixed
     */
    public function getSettingsParameter($namespace, $name)
    {
        $settings = $this->settingsManager->loadSettings($namespace);

        return $settings->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_settings';
    }
}
