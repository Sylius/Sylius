<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Resource\Metadata;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\Registry;
use Sylius\Component\Resource\Metadata\RegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Registry::class);
    }

    function it_implements_registry_interface()
    {
        $this->shouldImplement(RegistryInterface::class);
    }

    function it_returns_all_resources_metadata(MetadataInterface $metadata1, MetadataInterface $metadata2)
    {
        $metadata1->getAlias()->willReturn('app.product');
        $metadata1->getClass('model')->willReturn('Product');
        $metadata2->getAlias()->willReturn('app.order');
        $metadata2->getClass('model')->willReturn('Order');

        $this->add($metadata1);
        $this->add($metadata2);

        $this->getAll()->shouldReturn(['app.product' => $metadata1, 'app.order' => $metadata2]);
    }

    function it_throws_an_exception_if_resource_is_not_registered()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('get', ['foo.bar'])
        ;
    }

    function it_returns_specific_metadata(MetadataInterface $metadata)
    {
        $metadata->getAlias()->willReturn('app.shipping_method');
        $metadata->getClass('model')->willReturn('ShippingMethod');

        $this->add($metadata);

        $this->get('app.shipping_method')->shouldReturn($metadata);
    }

    function it_should_say_if_it_has_metadata_for_a_specific_alias(MetadataInterface $metadata)
    {
        $metadata->getAlias()->willReturn('app.shipping_method');
        $metadata->getClass('model')->willReturn('ShippingMethod');

        $this->add($metadata);

        $this->has('app.shipping_method')->shouldReturn(true);
        $this->has('app.order')->shouldReturn(false);
    }

    function it_throws_an_exception_if_resource_is_not_registered_with_class()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getByClass', ['App\Model\OrderItem'])
        ;
    }

    function it_returns_specific_metadata_by_model_class(MetadataInterface $metadata1, MetadataInterface $metadata2)
    {
        $metadata1->getAlias()->willReturn('app.product');
        $metadata1->getClass('model')->willReturn('App\Model\Product');

        $metadata2->getAlias()->willReturn('app.order');
        $metadata2->getClass('model')->willReturn('App\Model\Order');

        $this->add($metadata1);
        $this->add($metadata2);

        $this->getByClass('App\Model\Order')->shouldReturn($metadata2);
    }

    function it_can_say_if_it_contains_metadata_for_a_model_class(MetadataInterface $metadata)
    {
        $metadata->getAlias()->willReturn('app.product');
        $metadata->getClass('model')->willReturn('App\Model\Product');

        $this->add($metadata);

        $this->hasByClass('App\Model\Product')->shouldReturn(true);
        $this->hasByClass('App\Model\Order')->shouldReturn(false);
    }

    function it_adds_metadata_from_configuration_array()
    {
        $this->addFromAliasAndConfiguration('app.product', [
            'driver' => 'doctrine/orm',
            'classes' => [
                'model' => 'App\Model\Product',
            ],
        ]);

        $this->get('app.product')->shouldHaveType(MetadataInterface::class);
        $this->getByClass('App\Model\Product')->shouldHaveType(MetadataInterface::class);
    }
}
