<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NotificationWidgetExtension extends AbstractExtension
{
    public function __construct(private bool $areNotificationsEnabled, private int $checkFrequency)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_render_notifications_widget',
                [$this, 'renderWidget'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }

    public function renderWidget(Environment $environment): string
    {
        if (!$this->areNotificationsEnabled) {
            return '';
        }

        return $environment->render('@SyliusAdmin/_notification.html.twig', [
            'frequency' => $this->checkFrequency,
            'currentVersion' => SyliusCoreBundle::VERSION,
        ]);
    }
}
