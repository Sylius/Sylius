<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command\Admin;

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;

class RequestResetPasswordEmail implements LocaleCodeAwareInterface, IriToIdentifierConversionAwareInterface
{
    private ?string $locale;

    public function __construct(private string $email)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLocaleCode(): ?string
    {
        return $this->locale;
    }

    public function setLocaleCode(?string $localeCode): void
    {
        $this->locale = $localeCode;
    }

}
