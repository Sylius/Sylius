<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resource controller configuration factory.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class RequestConfigurationFactory implements RequestConfigurationFactoryInterface
{
    const API_VERSION_HEADER = 'Accept';
    const API_GROUPS_HEADER  = 'Accept';

    const API_VERSION_REGEXP = '/(v|version)=(?P<version>[0-9\.]+)/i';
    const API_GROUPS_REGEXP  = '/(g|groups)=(?P<groups>[a-z,_\s]+)/i';

    /**
     * @var ParametersParser
     */
    private $parametersParser;

    /**
     * @var string
     */
    private $configurationClass;

    /**
     * Default parameters.
     *
     * @var array
     */
    private $defaultParameters;

    /**
     * Constructor.
     *
     * @param ParametersParser $parametersParser
     * @param string $configurationClass
     * @param array $defaultParameters
     */
    public function __construct(ParametersParser $parametersParser, $configurationClass, array $defaultParameters = array())
    {
        $this->parametersParser = $parametersParser;
        $this->configurationClass = $configurationClass;
        $this->defaultParameters = $defaultParameters;
    }

    /**
     * Create configuration for given parameters.
     *
     * @return RequestConfiguration
     */
    public function create(ResourceMetadataInterface $metadata, Request $request)
    {
        $parameters = $this->parseApiParameters($request);
        $parameters = $this->parametersParser->parseRequestValues($parameters, $request);

        return new $this->configurationClass($metadata, $request, new Parameters($parameters));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function parseApiParameters(Request $request)
    {
        $parameters = array();

        if ($request->headers->has(self::API_VERSION_HEADER)) {
            if (preg_match(self::API_VERSION_REGEXP, $request->headers->get(self::API_VERSION_HEADER), $matches)) {
                $parameters['serialization_version'] = $matches['version'];
            }
        }

        if ($request->headers->has(self::API_GROUPS_HEADER)) {
            if (preg_match(self::API_GROUPS_REGEXP, $request->headers->get(self::API_GROUPS_HEADER), $matches)) {
                $parameters['serialization_groups'] = array_map('trim', explode(',', $matches['groups']));
            }
        }

        return array_merge($request->attributes->get('_sylius', array()), $parameters);
    }
}