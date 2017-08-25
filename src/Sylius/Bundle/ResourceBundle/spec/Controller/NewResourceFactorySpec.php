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

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\NewResourceFactory;
use Sylius\Bundle\ResourceBundle\Controller\NewResourceFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class NewResourceFactorySpec extends ObjectBehavior
{
    function it_implements_new_resource_factory_interface(): void
    {
        $this->shouldImplement(NewResourceFactoryInterface::class);
    }

    function it_calls_create_new_by_default_if_no_custom_method_configured(
        RequestConfiguration $requestConfiguration,
        FactoryInterface $factory,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getFactoryMethod()->willReturn(null);

        $factory->createNew()->willReturn($resource);

        $this->create($requestConfiguration, $factory)->shouldReturn($resource);
    }

    function it_calls_proper_factory_methods_based_on_configuration(
        RequestConfiguration $requestConfiguration,
        FactoryInterface $factory,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getFactoryMethod()->willReturn('createNew');
        $requestConfiguration->getFactoryArguments()->willReturn(['00032']);

        $factory->createNew('00032')->willReturn($resource);

        $this->create($requestConfiguration, $factory)->shouldReturn($resource);
    }
}
