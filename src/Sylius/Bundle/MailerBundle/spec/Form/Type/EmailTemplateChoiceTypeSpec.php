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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmailTemplateChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array(
            'template' => 'my_template',
        ));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MailerBundle\Form\Type\EmailTemplateChoiceType');
    }

    public function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array('my_template' => 'template'),
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    public function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_email_template_choice');
    }
}
