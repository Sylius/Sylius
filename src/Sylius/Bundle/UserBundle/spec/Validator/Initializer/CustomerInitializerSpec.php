<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Validator\Initializer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class CustomerInitializerSpec extends ObjectBehavior
{
    function let(CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith($canonicalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Validator\Initializer\CustomerInitializer');
    }

    function it_implements_symfony_validator_initializer_interface()
    {
        $this->shouldImplement(ObjectInitializerInterface::class);
    }

    function it_sets_canonical_email_when_initializing_customer($canonicalizer, CustomerInterface $customer)
    {
        $customer->getEmail()->willReturn('sTeFfEn@gMaiL.CoM');
        $canonicalizer->canonicalize('sTeFfEn@gMaiL.CoM')->willReturn('steffen@gmail.com');
        $customer->setEmailCanonical('steffen@gmail.com')->shouldBeCalled();

        $this->initialize($customer);
    }

    function it_does_not_set_canonical_email_when_initializing_non_customer_object(\stdClass $object)
    {
        $this->initialize($object);
    }
}
