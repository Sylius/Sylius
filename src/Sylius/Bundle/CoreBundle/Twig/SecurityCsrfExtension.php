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

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SecurityCsrfExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $adminCsrfParameter,
        private readonly string $adminCsrfTokenId,
        private readonly string $shopCsrfParameter,
        private readonly string $shopCsrfTokenId,
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
        return match ($section) {
            'admin' => $this->adminCsrfParameter,
            'shop' => $this->shopCsrfParameter,
            default => throw new \InvalidArgumentException(sprintf('Unknown section "%s". Expected "admin" or "shop".', $section)),
        };
    }

    public function getCsrfTokenId(string $section): string
    {
        return match ($section) {
            'admin' => $this->adminCsrfTokenId,
            'shop' => $this->shopCsrfTokenId,
            default => throw new \InvalidArgumentException(sprintf('Unknown section "%s". Expected "admin" or "shop".', $section)),
        };
    }
}
