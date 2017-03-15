<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Twig;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class NotificationExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var bool
     */
    private $areNotificationsEnabled;

    /**
     * @param bool $areNotificationsEnabled
     */
    public function __construct($areNotificationsEnabled)
    {
        $this->areNotificationsEnabled = $areNotificationsEnabled;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return [
            'sylius_version_notification_enabled' => $this->areNotificationsEnabled,
        ];
    }
}
