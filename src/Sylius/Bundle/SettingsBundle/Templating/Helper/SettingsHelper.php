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
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    public function getSettings($namespace)
    {
        return $this->settingsManager->loadSettings($namespace);
    }

    /**
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
     * Checks if settings parameter for given namespace and name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasSettingsParameter($name)
    {
        if (false === strpos($name, '.')) {
            throw new \InvalidArgumentException(sprintf('Parameter must be in format "namespace.name", "%s" given.', $name));
        }

        list($namespace, $name) = explode('.', $name);

        $settings = $this->settingsManager->loadSettings($namespace);

        return $settings->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_settings';
    }
}
