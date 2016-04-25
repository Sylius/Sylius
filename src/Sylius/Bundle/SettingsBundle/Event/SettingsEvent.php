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

use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class SettingsEvent extends Event
{
    const PRE_SAVE = 'sylius.settings.pre_save';
    const POST_SAVE = 'sylius.settings.post_save';

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return SettingsInterface
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param SettingsInterface $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }
}
