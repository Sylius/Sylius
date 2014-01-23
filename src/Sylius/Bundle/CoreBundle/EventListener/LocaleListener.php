<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

/**
 * Sets currently selected locale on request object.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LocaleListener
{
    protected $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    public function setRequestLocale(GetResponseEvent $event)
    {
        $event->getRequest()->setLocale(
            $this->settingsManager->loadSettings('general')->get('locale')
        );
    }
}
