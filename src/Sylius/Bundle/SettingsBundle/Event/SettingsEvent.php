<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Event;

use Sylius\Bundle\SettingsBundle\Model\Settings;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Settings event.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class SettingsEvent extends GenericEvent
{
    const PRE_SAVE = 'sylius.settings.pre_save';
    const POST_SAVE = 'sylius.settings.post_save';

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param string $namespace
     * @param Settings $settings
     * @param array $parameters
     */
    public function __construct($namespace, Settings $settings, array $parameters)
    {
        $this->namespace = $namespace;
        $this->settings = $settings;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param Settings $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}