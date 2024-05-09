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

namespace Sylius\Bundle\ApiBundle\Security;

use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CustomerVoter extends Voter
{
    public const SYLIUS_CUSTOMER = 'SYLIUS_CUSTOMER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::SYLIUS_CUSTOMER === $attribute) {
            return true;
        }

        return false;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return self::SYLIUS_CUSTOMER === $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof ShopUserInterface) {
            return false;
        }

        return in_array('ROLE_USER', $user->getRoles(), true) && null !== $user->getCustomer();
    }
}
