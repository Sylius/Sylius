<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\FlowBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\FlowBundle\Storage\SessionFlowsBag;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;

class SessionFlowsBagSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\FlowBundle\Storage\SessionFlowsBag');
    }

    function it_is_a_namespace_attribute_bag()
    {
        $this->shouldHaveType(NamespacedAttributeBag::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(SessionFlowsBag::NAME);
    }
}
