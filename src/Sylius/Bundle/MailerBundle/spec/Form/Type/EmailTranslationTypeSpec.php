<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MailerBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
class EmailTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ClassName', ['ValidationGroup1', 'ValidationGroup2']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MailerBundle\Form\Type\EmailTranslationType');
    }

    function it_builds_its_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('subject', 'text', array('label' => 'sylius.form.email.subject'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('content', 'textarea', array('label' => 'sylius.form.email.content'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_gets_its_name()
    {
        $this->getName()->shouldReturn('sylius_email_translation');
    }
}
