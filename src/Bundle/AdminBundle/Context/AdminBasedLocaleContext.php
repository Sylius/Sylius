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

namespace Sylius\Bundle\AdminBundle\Context;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AdminBasedLocaleContext implements LocaleContextInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function getLocaleCode(): string
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw new LocaleNotFoundException();
        }

        $user = $token->getUser();
        if (!$user instanceof AdminUserInterface) {
            throw new LocaleNotFoundException();
        }

        return $user->getLocaleCode();
    }
}
