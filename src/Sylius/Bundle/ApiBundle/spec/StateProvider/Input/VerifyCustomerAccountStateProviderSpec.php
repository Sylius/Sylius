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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Input;

use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class VerifyCustomerAccountStateProviderSpec extends ObjectBehavior
{
    function it_returns_null_when_operation_is_not_on_verify_customer_account(Operation $operation): void
    {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->provide($operation)->shouldReturn(null);
    }

    function it_throw_http_exception_when_no_token_is_provided(Operation $operation): void
    {
        $operation->getClass()->willReturn(VerifyCustomerAccount::class);

        $this
            ->shouldThrow(HttpException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_returns_verify_customer_account_command_when_token_is_provided(Operation $operation): void
    {
        $operation->getClass()->willReturn(VerifyCustomerAccount::class);

        $this
            ->provide($operation, ['token' => 'TOKEN'])
            ->shouldBeLike(new VerifyCustomerAccount('TOKEN'))
        ;
    }
}
