<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Form\Builder\DefaultFormBuilderInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @mixin DefaultResourceType
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DefaultResourceTypeSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata, DefaultFormBuilderInterface $defaultFormBuilder)
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('order_item');

        $this->beConstructedWith($metadata, $defaultFormBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_extends_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_the_form_using_default_form_builder(
        MetadataInterface $metadata,
        FormBuilderInterface $formBuilder,
        DefaultFormBuilderInterface $defaultFormBuilder
    ) {
        $defaultFormBuilder->build($metadata, $formBuilder, [])->shouldBeCalled();

        $this->buildForm($formBuilder, []);
    }

    function it_generates_name_from_metadata(MetadataInterface $metadata)
    {
        $this->getName()->shouldReturn('sylius_order_item');
    }
}
