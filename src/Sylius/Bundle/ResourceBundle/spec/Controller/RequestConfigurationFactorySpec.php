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
use Sylius\Bundle\ResourceBundle\Controller\ParametersParserInterface;
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
final class RequestConfigurationFactorySpec extends ObjectBehavior
{
    function let(ParametersParserInterface $parametersParser)
    {
        $this->beConstructedWith($parametersParser, RequestConfiguration::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestConfigurationFactory::class);
    }

    function it_implements_request_configuration_factory_interface()
    {
        $this->shouldImplement(RequestConfigurationFactoryInterface::class);
    }

    function it_creates_configuration_from_resource_metadata_and_request(
        ParametersParserInterface $parametersParser,
        MetadataInterface $metadata,
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $attributesBag
    ) {
        $request->headers = $headersBag;
        $request->attributes = $attributesBag;

        $headersBag->get('Accept')->willReturn(null);

        $attributesBag->get('_sylius', [])->willReturn(['template' => ':Product:show.html.twig']);
        $parametersParser
            ->parseRequestValues(['template' => ':Product:show.html.twig'], $request)
            ->willReturn(['template' => ':Product:list.html.twig'])
        ;

        $this->create($metadata, $request)->shouldHaveType(RequestConfiguration::class);
    }

    function it_creates_configuration_without_default_settings(
        ParametersParserInterface $parametersParser,
        MetadataInterface $metadata,
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $attributesBag
    ) {
        $request->headers = $headersBag;
        $request->attributes = $attributesBag;

        $attributesBag->get('_sylius', [])->willReturn(['template' => ':Product:list.html.twig']);
        $parametersParser
            ->parseRequestValues(['template' => ':Product:list.html.twig'], $request)
            ->willReturn(['template' => ':Product:list.html.twig'])
        ;

        $this->create($metadata, $request)->isSortable()->shouldReturn(false);
    }

    function it_creates_configuration_with_default_settings(
        ParametersParserInterface $parametersParser,
        MetadataInterface $metadata,
        Request $request,
        ParameterBag $headersBag,
        ParameterBag $attributesBag
    ) {
        $this->beConstructedWith($parametersParser, RequestConfiguration::class, ['sortable' => true]);

        $request->headers = $headersBag;
        $request->attributes = $attributesBag;

        $attributesBag->get('_sylius', [])->willReturn(['template' => ':Product:list.html.twig']);

        $parametersParser
            ->parseRequestValues(['template' => ':Product:list.html.twig', 'sortable' => true], $request)
            ->willReturn(['template' => ':Product:list.html.twig', 'sortable' => true])
        ;

        $this->create($metadata, $request)->isSortable()->shouldReturn(true);
    }
}
