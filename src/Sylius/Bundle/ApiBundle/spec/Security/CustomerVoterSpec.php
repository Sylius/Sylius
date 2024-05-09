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

namespace spec\Sylius\Bundle\ApiBundle\Security;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Security\CustomerVoter;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class CustomerVoterSpec extends ObjectBehavior
{
    public function it_does_not_support_wrong_attribute(
        TokenInterface $token,
    ): void {
        $this->vote($token, null, ['WRONG_ATTRIBUTE'])->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    public function it_denies_access_when_user_is_null(
        TokenInterface $token,
    ): void {
        $token->getUser()->willReturn(null);

        $this->vote($token, null, [CustomerVoter::SYLIUS_CUSTOMER])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    public function it_denies_access_when_user_is_not_shop_user(
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $token->getUser()->willReturn($adminUser);

        $this->vote($token, null, [CustomerVoter::SYLIUS_CUSTOMER])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    public function it_denies_access_when_user_does_not_have_role_user(
        TokenInterface $token,
        ShopUserInterface $shopUser,
    ): void {
        $shopUser->getRoles()->willReturn(['ROLE_TEST']);

        $token->getUser()->willReturn($shopUser);

        $this->vote($token, null, [CustomerVoter::SYLIUS_CUSTOMER])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    public function it_denies_access_when_user_does_not_have_customer(
        TokenInterface $token,
        ShopUserInterface $shopUser,
    ): void {
        $shopUser->getRoles()->willReturn(['ROLE_USER']);
        $shopUser->getCustomer()->willReturn(null);

        $token->getUser()->willReturn($shopUser);

        $this->vote($token, null, [CustomerVoter::SYLIUS_CUSTOMER])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    public function it_grants_access_when_user_has_role_user_and_customer(
        TokenInterface $token,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
    ): void {
        $shopUser->getRoles()->willReturn(['ROLE_USER']);
        $shopUser->getCustomer()->willReturn($customer);

        $token->getUser()->willReturn($shopUser);

        $this->vote($token, null, [CustomerVoter::SYLIUS_CUSTOMER])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }
}
