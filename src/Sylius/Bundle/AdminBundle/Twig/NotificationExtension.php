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

use Sylius\Bundle\CoreBundle\Application\Kernel;

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
     * @var int
     */
    private $checkFrequency;

    /**
     * @param bool $areNotificationsEnabled
     * @param int $checkFrequency
     */
    public function __construct($areNotificationsEnabled, $checkFrequency)
    {
        $this->areNotificationsEnabled = $areNotificationsEnabled;
        $this->checkFrequency = $checkFrequency;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'sylius_render_notification_widget',
                [$this, 'renderWidget'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
        ];
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

    /**
     * @param \Twig_Environment $environment
     *
     * @return string
     */
    public function renderWidget(\Twig_Environment $environment)
    {
        return $environment->render('@SyliusAdmin/_notification.html.twig', [
            'frequency' => $this->checkFrequency,
            'version' => Kernel::VERSION,
        ]);
    }
}
