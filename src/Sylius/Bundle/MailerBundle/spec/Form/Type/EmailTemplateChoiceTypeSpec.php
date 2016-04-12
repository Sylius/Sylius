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
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailTemplateChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'template' => 'my_template',
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MailerBundle\Form\Type\EmailTemplateChoiceType');
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => ['template' => 'my_template'],
            'choices_as_values' => true,
        ])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_email_template_choice');
    }
}
