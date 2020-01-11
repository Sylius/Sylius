<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Validator\Initializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Validator\ObjectInitializerInterface;

final class CustomerInitializerSpec extends ObjectBehavior
{
    function let(CanonicalizerInterface $canonicalizer): void
    {
        $this->beConstructedWith($canonicalizer);
    }

    function it_implements_symfony_validator_initializer_interface(): void
    {
        $this->shouldImplement(ObjectInitializerInterface::class);
    }

    function it_sets_canonical_email_when_initializing_customer($canonicalizer, CustomerInterface $customer): void
    {
        $customer->getEmail()->willReturn('sTeFfEn@gMaiL.CoM');
        $canonicalizer->canonicalize('sTeFfEn@gMaiL.CoM')->willReturn('steffen@gmail.com');
        $customer->setEmailCanonical('steffen@gmail.com')->shouldBeCalled();

        $this->initialize($customer);
    }

    function it_does_not_set_canonical_email_when_initializing_non_customer_object(\stdClass $object): void
    {
        $this->initialize($object);
    }
}
