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

namespace Sylius\Bundle\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CsrfProtectionEnabledExtension extends AbstractExtension
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_csrf_protection_enabled', [$this, 'isCsrfProtectionEnabled']),
        ];
    }

    public function isCsrfProtectionEnabled(): bool
    {
        return $this->container->has('security.csrf.token_manager');
    }
}
