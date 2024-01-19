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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Customer\RemoveShopUser;
use Sylius\Bundle\UserBundle\Exception\UserNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class RemoveShopUserHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository);
    }

    public function it_throws_an_exception_if_user_has_not_been_found(UserRepositoryInterface $userRepository): void
    {
        $userRepository->find(42)->willReturn(null);

        $this->shouldThrow(UserNotFoundException::class)->during('__invoke', [new RemoveShopUser(42)]);
    }

    public function it_should_remove_shop_user(UserRepositoryInterface $userRepository, ShopUserInterface $shopUser): void
    {
        $userRepository->find(42)->willReturn($shopUser);
        $shopUser->setCustomer(null)->shouldBeCalled();
        $userRepository->remove($shopUser)->shouldBeCalled();

        $this->__invoke(new RemoveShopUser(42));
    }
}
