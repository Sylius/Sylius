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

namespace Sylius\Bundle\CoreBundle\OAuth\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

final class UnverifiedEmailException extends CustomUserMessageAuthenticationException
{
    public const MESSAGE_KEY = 'The e-mail address has not been confirmed by %resource_owner%.';

    public function __construct(string $resourceOwnerName)
    {
        parent::__construct(sprintf(
            'The "%s" resource owner did not confirm that the returned e-mail address is verified, so it cannot be matched with data already present in the shop.',
            $resourceOwnerName,
        ));

        $this->setSafeMessage(self::MESSAGE_KEY, ['%resource_owner%' => $resourceOwnerName]);
    }
}
