<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Metadata;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\Resource\Metadata\MetadataInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class MetadataSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromAliasAndConfiguration', [
            'app.product',
            [
                'driver' => 'doctrine/orm',
                'templates' => 'AppBundle:Resource',
                'classes' => [
                    'model' => 'AppBundle\Model\Resource',
                    'form' => [
                        'default' => 'AppBundle\Form\Type\ResourceType',
                        'choice' => 'AppBundle\Form\Type\ResourceChoiceType',
                        'autocomplete' => 'AppBundle\Type\ResourceAutocompleteType',
                    ],
                ],
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Metadata::class);
    }

    function it_implements_metadata_interface()
    {
        $this->shouldImplement(MetadataInterface::class);
    }

    function it_has_alias()
    {
        $this->getAlias()->shouldReturn('app.product');
    }

    function it_has_application_name()
    {
        $this->getApplicationName()->shouldReturn('app');
    }

    function it_has_resource_name()
    {
        $this->getName()->shouldReturn('product');
    }

    function it_humanizes_simple_names()
    {
        $this->getHumanizedName()->shouldReturn('product');
    }

    function it_humanizes_more_complex_names()
    {
        $this->beConstructedThrough('fromAliasAndConfiguration', ['app.product_option', ['driver' => 'doctrine/orm']]);

        $this->getHumanizedName()->shouldReturn('product option');
    }

    function it_has_plural_resource_name()
    {
        $this->getPluralName()->shouldReturn('products');
    }

    function it_has_driver()
    {
        $this->getDriver()->shouldReturn('doctrine/orm');
    }

    function it_has_templates_namespace()
    {
        $this->getTemplatesNamespace()->shouldReturn('AppBundle:Resource');
    }

    function it_has_access_to_specific_config_parameter()
    {
        $this->getParameter('driver')->shouldReturn('doctrine/orm');
    }

    function it_checks_if_specific_parameter_exists()
    {
        $this->hasParameter('foo')->shouldReturn(false);
        $this->hasParameter('driver')->shouldReturn(true);
    }

    function it_throws_an_exception_when_parameter_does_not_exist()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getParameter', ['foo'])
        ;
    }

    function it_has_access_to_specific_classes()
    {
        $this->getClass('model')->shouldReturn('AppBundle\Model\Resource');
    }

    function it_throws_an_exception_when_class_does_not_exist()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getClass', ['foo'])
        ;
    }

    function it_checks_if_specific_class_exists()
    {
        $this->hasClass('bar')->shouldReturn(false);
        $this->hasClass('model')->shouldReturn(true);
    }

    function it_generates_service_id()
    {
        $this->getServiceId('factory')->shouldReturn('app.factory.product');
        $this->getServiceId('repository')->shouldReturn('app.repository.product');
        $this->getServiceId('form.type')->shouldReturn('app.form.type.product');
    }

    function it_generates_permission_code()
    {
        $this->getPermissionCode('show')->shouldReturn('app.product.show');
        $this->getPermissionCode('create')->shouldReturn('app.product.create');
        $this->getPermissionCode('custom')->shouldReturn('app.product.custom');
    }
}
