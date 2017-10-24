<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Bundle\CoreBundle\Application\Kernel;

final class NotificationWidgetExtension extends \Twig_Extension
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
    public function __construct(bool $areNotificationsEnabled, int $checkFrequency)
    {
        $this->areNotificationsEnabled = $areNotificationsEnabled;
        $this->checkFrequency = $checkFrequency;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function(
                'sylius_render_notifications_widget',
                [$this, 'renderWidget'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     *
     * @return string
     */
    public function renderWidget(\Twig_Environment $environment): string
    {
        if (!$this->areNotificationsEnabled) {
            return '';
        }

        return $environment->render('@SyliusAdmin/_notification.html.twig', [
            'frequency' => $this->checkFrequency,
            'currentVersion' => Kernel::VERSION,
        ]);
    }
}
