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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Component\Core\Model\AddressInterface;

final class AccountResetPasswordItemDataProviderSpec extends ObjectBehavior
{
    function it_supports_only_reset_password(): void
    {
        $this->supports(ResetPassword::class, 'post')->shouldReturn(true);
        $this->supports(AddressInterface::class, 'post')->shouldReturn(false);
    }

    function it_provides_reset_password_class(): void
    {
        $this
            ->getItem(ResetPassword::class, 'resetToken')
            ->shouldBeLike(new ResetPassword('resetToken'))
        ;
    }
}
