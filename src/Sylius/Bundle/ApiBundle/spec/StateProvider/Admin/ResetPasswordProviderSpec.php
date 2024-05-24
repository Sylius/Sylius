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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Admin;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;

final class ResetPasswordProviderSpec extends ObjectBehavior
{
    function it_provides_reset_password_class(): void
    {
        $operation = new Patch(class: ResetPassword::class);

        $this
            ->provide($operation, ['token' => 'resetToken'])
            ->shouldBeLike(new ResetPassword('resetToken'))
        ;
    }

    function it_throws_an_exception_when_operation_is_not_patch(Operation $operation): void
    {
        $operation->getClass()->willReturn(ResetPassword::class);

        $this->shouldThrow(\RuntimeException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_provides_nothing_if_operation_is_not_reset_password(): void
    {
        $operation = new Patch(class: 'NotResetPassword');

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }
}
