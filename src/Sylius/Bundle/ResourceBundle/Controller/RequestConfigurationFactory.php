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

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;

final class RequestConfigurationFactory implements RequestConfigurationFactoryInterface
{
    private const API_VERSION_HEADER = 'Accept';
    private const API_GROUPS_HEADER = 'Accept';

    private const API_VERSION_REGEXP = '/(v|version)=(?P<version>[0-9\.]+)/i';
    private const API_GROUPS_REGEXP = '/(g|groups)=(?P<groups>[a-z,_\s]+)/i';

    /**
     * @var ParametersParserInterface
     */
    private $parametersParser;

    /**
     * @var string
     */
    private $configurationClass;

    /**
     * @var array
     */
    private $defaultParameters;

    /**
     * @param string $configurationClass
     * @param array $defaultParameters
     */
    public function __construct(ParametersParserInterface $parametersParser, string $configurationClass, array $defaultParameters = [])
    {
        $this->parametersParser = $parametersParser;
        $this->configurationClass = $configurationClass;
        $this->defaultParameters = $defaultParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function create(MetadataInterface $metadata, Request $request): RequestConfiguration
    {
        $parameters = array_merge($this->defaultParameters, $this->parseApiParameters($request));
        $parameters = $this->parametersParser->parseRequestValues($parameters, $request);

        return new $this->configurationClass($metadata, $request, new Parameters($parameters));
    }

    /**
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function parseApiParameters(Request $request): array
    {
        $parameters = [];

        $apiVersionHeaders = $request->headers->get(self::API_VERSION_HEADER, null, false);
        foreach ($apiVersionHeaders as $apiVersionHeader) {
            if (preg_match(self::API_VERSION_REGEXP, $apiVersionHeader, $matches)) {
                $parameters['serialization_version'] = $matches['version'];
            }
        }

        $apiGroupsHeaders = $request->headers->get(self::API_GROUPS_HEADER, null, false);
        foreach ($apiGroupsHeaders as $apiGroupsHeader) {
            if (preg_match(self::API_GROUPS_REGEXP, $apiGroupsHeader, $matches)) {
                $parameters['serialization_groups'] = array_map('trim', explode(',', $matches['groups']));
            }
        }

        return array_merge($request->attributes->get('_sylius', []), $parameters);
    }
}
