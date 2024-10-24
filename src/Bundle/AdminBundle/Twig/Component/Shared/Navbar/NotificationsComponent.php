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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar;

use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class NotificationsComponent
{
    /**
     * @return array<string, mixed>
     */
    #[ExposeInTemplate(name: 'notifications')]
    public function getNotifications(): array
    {
        return [];
    }
}
