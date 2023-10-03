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
use Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount;
use Sylius\Component\Resource\Model\ResourceInterface;

final class VerifyCustomerAccountItemDataProviderSpec extends ObjectBehavior
{
    function it_supports_only_verify_customer_account(): void
    {
        $this->supports(VerifyCustomerAccount::class, 'post')->shouldReturn(true);
        $this->supports(ResourceInterface::class, 'post')->shouldReturn(false);
    }

    function it_creates_and_provides_verify_customer_account_class(): void
    {
        $this
            ->getItem(VerifyCustomerAccount::class, 'ToKeN')
            ->shouldBeLike(new VerifyCustomerAccount('ToKeN'))
        ;
    }
}
