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

use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelper;

/**
 * Sylius settings extension for Twig.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsExtension extends \Twig_Extension
{
    /**
     * @var SettingsHelper
     */
    private $helper;

    /**
     * @param SettingsHelper $helper
     */
    public function __construct(SettingsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
             new \Twig_SimpleFunction('sylius_settings_all', [$this, 'getSettings']),
             new \Twig_SimpleFunction('sylius_settings_get', [$this, 'getSettingsParameter']),
             new \Twig_SimpleFunction('sylius_settings_has', [$this, 'hasSettingsParameter']),
        ];
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    public function getSettings($namespace)
    {
        return $this->helper->getSettings($namespace);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getSettingsParameter($name)
    {
        return $this->helper->getSettingsParameter($name);
    }

    /**
     * Checks if settings parameter for given namespace and name exists.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function hasSettingsParameter($name)
    {
        return $this->helper->hasSettingsParameter($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_settings';
    }
}
