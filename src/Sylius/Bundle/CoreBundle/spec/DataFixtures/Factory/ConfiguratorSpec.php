<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Configurator;
use Sylius\Bundle\CoreBundle\Tests\Application\Fixtures\Factory\AddressFactory;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Product\Model\Product;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;

final class ConfiguratorSpec extends ObjectBehavior
{
    function let(RegistryInterface $registry): void
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Configurator::class);
    }

    function it_configures_model_class(
        RegistryInterface $registry,
        MetadataInterface $firstMetadata,
        MetadataInterface $secondMetadata,
        MetadataInterface $thirdMetadata,
    ): void {
        $registry->getAll()->willReturn([$firstMetadata->getWrappedObject(), $secondMetadata->getWrappedObject(), $thirdMetadata->getWrappedObject()]);

        $firstMetadata->getClass('model')->willReturn(Product::class)->shouldBeCalled();
        $secondMetadata->getClass('model')->willReturn(Address::class)->shouldBeCalled();

        $thirdMetadata->getClass('model')->willReturn(Currency::class)->shouldNotBeCalled();

        $this->configure(new AddressFactory());
    }
}
