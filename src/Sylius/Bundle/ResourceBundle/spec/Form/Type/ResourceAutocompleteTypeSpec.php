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
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceAutocompleteToIdentifierTransformer;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceAutocompleteTypeSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata)
    {
        $this->beConstructedWith($metadata) ;
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteType');
    }

    function it_has_a_name($metadata)
    {
        $metadata->getName()->willReturn('name');
        $metadata->getApplicationName()->willReturn('sylius');

        $this->getName()->shouldReturn('sylius_name_autocomplete');
    }

    function it_builds_a_form(FormBuilderInterface $builder, $metadata)
    {
        $metadata->getName()->willReturn('name');
        $metadata->getApplicationName()->willReturn('sylius');

        $builder
            ->add('select', 'choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('resource', 'sylius_name_to_hidden_identifier')
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->addModelTransformer(Argument::type(ResourceAutocompleteToIdentifierTransformer::class))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, ['select' => 'name']);
    }

    function it_has_default_configure_options(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'select' => 'name',
                'data_class' => null,
            ])
            ->shouldBeCalled()
            ->willReturn($resolver)
        ;

        $this->configureOptions($resolver);
    }
}
