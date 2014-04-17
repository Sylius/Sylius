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
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
        return array(
             new \Twig_SimpleFunction('sylius_settings_all', array($this, 'getSettings')),
             new \Twig_SimpleFunction('sylius_settings_get', array($this, 'getSettingsParameter')),
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
        return $this->helper->getSettings($namespace);
    }

    /**
     * Load settings parameter for given namespace and name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getSettingsParameter($name)
    {
        return $this->helper->getSettingsParameter($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_settings';
    }
}
