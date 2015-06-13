<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerRegistrationTypeSpec extends ObjectBehavior
{
    function let(RepositoryInterface $customerRepository)
    {
        $this->beConstructedWith('Customer', array('sylius'), $customerRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Type\CustomerRegistrationType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('firstName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('lastName', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('email', 'email', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->add('user', 'sylius_user_registration', Argument::type('array'))->shouldbeCalled()->willReturn($builder);
        $builder->addEventSubscriber(
            Argument::type('Sylius\Bundle\UserBundle\Form\EventListener\CustomerRegistrationFormListener')
        )->shouldbeCalled()->willReturn($builder);
        $builder->addEventSubscriber(
            Argument::type('Sylius\Bundle\UserBundle\Form\EventListener\UserRegistrationFormListener')
        )->shouldbeCalled()->willReturn($builder);
        $builder->setDataLocked(false)->shouldbeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Customer',
            'validation_groups' => array('sylius'),
            'cascade_validation' => true
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_customer_registration');
    }
}
