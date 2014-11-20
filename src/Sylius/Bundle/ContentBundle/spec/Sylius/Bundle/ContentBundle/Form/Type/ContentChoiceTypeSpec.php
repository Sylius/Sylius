<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ContentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('My\Content\Model');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\ContentChoiceType');
    }

    function it_has_default_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'My\Content\Model',
            'property' => 'title',
            'required' => false
        ));

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getParent()->shouldReturn('sylius_content_choice');
    }

    function it_has_a_parent()
    {
        $this->getName()->shouldReturn('phpcr_document');
    }
}
