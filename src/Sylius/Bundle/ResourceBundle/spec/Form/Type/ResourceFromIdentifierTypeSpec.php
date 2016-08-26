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
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\IdentifierToResourceTransformer;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ResourceFromIdentifierTypeSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository, MetadataInterface $metadata)
    {
        $this->beConstructedWith($repository, $metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ResourceFromIdentifierType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder
            ->addModelTransformer(Argument::type(IdentifierToResourceTransformer::class))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, ['identifier' => 'identifier']);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['identifier' => 'id'])
            ->shouldBeCalled()
            ->willReturn($resolver)
        ;

        $resolver
            ->setAllowedTypes('identifier', 'string')
            ->shouldBeCalled()
            ->willReturn($resolver)
        ;

        $this->configureOptions($resolver);
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('entity');
    }

    function it_has_a_name(MetadataInterface $metadata)
    {
        $metadata->getName()->willReturn('product');
        $metadata->getApplicationName()->willReturn('sylius');

        $this->getName()->shouldReturn('sylius_product_from_identifier');
    }
}
