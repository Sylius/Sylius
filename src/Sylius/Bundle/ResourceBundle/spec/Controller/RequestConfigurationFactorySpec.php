<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParser;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactory;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin RequestConfigurationFactory
 *
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RequestConfigurationFactorySpec extends ObjectBehavior
{
    function let(ParametersParser $parametersParser)
    {
        $this->beConstructedWith($parametersParser, RequestConfiguration::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactory');
    }

    function it_implements_request_configuration_factory_interface()
    {
        $this->shouldImplement(RequestConfigurationFactoryInterface::class);
    }

    function it_creates_configuration_from_resource_metadata_and_request(
        MetadataInterface $metadata,
        Request $request,
        ParametersParser $parametersParser,
        ParameterBag $headersBag,
        ParameterBag $attributesBag
    ) {
        $request->headers = $headersBag;
        $request->attributes = $attributesBag;

        $headersBag->has('Accept')->willReturn(false);
        $headersBag->has('Accept')->willReturn(false);

        $configuration = ['template' => ':Product:show.html.twig'];

        $attributesBag->get('_sylius', [])->shouldBeCalled()->willReturn($configuration);
        $parametersParser->parseRequestValues($configuration, $request)->willReturn($configuration);

        $this->create($metadata, $request)->shouldHaveType(RequestConfiguration::class);
    }
}
