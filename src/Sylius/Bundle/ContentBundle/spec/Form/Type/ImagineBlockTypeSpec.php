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

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\Test\FormBuilderInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ImagineBlockTypeSpec extends ObjectBehavior
{
    function let(FilterConfiguration $filterConfiguration)
    {
        $this->beConstructedWith('My\Resource\Model', ['validation_group'], $filterConfiguration);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\ImagineBlockType');
    }

    function it_builds_a_form($filterConfiguration, FormBuilderInterface $builder)
    {
        $filterConfiguration->all()->shouldBeCalled()->willReturn(['filter' => '']);

        $builder->add('parentDocument', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('name', 'text', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('label', 'text', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('linkUrl', 'text', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('filter', 'choice', Argument::type('array'))->willReturn($builder);
        $builder->add('image', 'cmf_media_image', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('publishable', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('publishStartDate', 'datetime', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('publishEndDate', 'datetime', Argument::type('array'))->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_imagine_block');
    }
}
