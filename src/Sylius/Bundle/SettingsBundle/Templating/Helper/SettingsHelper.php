<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Templating\Helper;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\Templating\Helper\Helper;

class SettingsHelper extends Helper
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
     * @param string $name
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function getSettingsParameter($name)
    {
        if (false === strpos($name, '.')) {
            throw new \InvalidArgumentException(sprintf('Parameter must be in format "namespace.name", "%s" given.', $name));
        }

        list($namespace, $name) = explode('.', $name);

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
