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

final class SecurityCsrfExtension extends AbstractExtension
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_security_csrf_parameter', [$this, 'getCsrfParameter']),
            new TwigFunction('sylius_security_csrf_token_id', [$this, 'getCsrfTokenId']),
        ];
    }

    public function getCsrfParameter(string $section): string
    {
        return (string) $this->container->getParameter(sprintf('sylius_%s.security.csrf_parameter', $section));
    }

    public function getCsrfTokenId(string $section): string
    {
        return (string) $this->container->getParameter(sprintf('sylius_%s.security.csrf_token_id', $section));
    }
}
