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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class NewResourceFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NewResourceFactory::class);
    }

    function it_implements_new_resource_factory_interface()
    {
        $this->shouldImplement(NewResourceFactoryInterface::class);
    }

    function it_calls_create_new_by_default_if_no_custom_method_configured(RequestConfiguration $requestConfiguration, FactoryInterface $factory)
    {
        $requestConfiguration->getFactoryMethod()->willReturn(null);

        $factory->createNew()->willReturn(['foo', 'bar']);

        $this->create($requestConfiguration, $factory)->shouldReturn(['foo', 'bar']);
    }

    function it_calls_proper_factory_methods_based_on_configuration(RequestConfiguration $requestConfiguration, FactoryInterface $factory)
    {
        $requestConfiguration->getFactoryMethod()->willReturn('createNew');
        $requestConfiguration->getFactoryArguments()->willReturn(['00032']);

        $factory->createNew('00032')->willReturn(['foo', 'bar']);

        $this->create($requestConfiguration, $factory)->shouldReturn(['foo', 'bar']);
    }
}
