<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Form\Type\CustomerGuestType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class CustomerGuestTypeSpec extends ObjectBehavior
{
    function let(EventSubscriberInterface $setCustomerFormSubscriber)
    {
        $this->beConstructedWith('Customer', ['sylius'], $setCustomerFormSubscriber);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerGuestType::class);
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(AbstractResourceType::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder, EventSubscriberInterface $setCustomerFormSubscriber)
    {
        $builder->add('email', 'email', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->addEventSubscriber($setCustomerFormSubscriber)->shouldbeCalled()->willReturn($builder);
        $builder->setDataLocked(Argument::type('bool'))->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_customer_guest');
    }
}
