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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;

final class ShopUserResetPasswordProviderSpec extends ObjectBehavior
{
    function it_throws_an_exception_if_operation_class_is_not_reset_password(Operation $operation): void
    {
        $operation->getClass()->willReturn('NotResetPasswordClass');
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation, [], []])
        ;
    }

    function it_provides_reset_password_object_if_operation_is_patch(): void
    {
        $operation = new Patch(class: ResetPassword::class);
        $uriVariables = ['token' => 'TOKEN'];

        $this
            ->provide($operation, $uriVariables)
            ->shouldBeLike(new ResetPassword('TOKEN'));
    }

    function it_throws_an_exception_if_operation_is_different_than_patch(): void
    {
        $operation = new Get(class: ResetPassword::class);
        $uriVariables = ['token' => 'TOKEN'];

        $this
            ->shouldThrow(\RuntimeException::class)
            ->during('provide', [$operation, $uriVariables])
        ;
    }
}
