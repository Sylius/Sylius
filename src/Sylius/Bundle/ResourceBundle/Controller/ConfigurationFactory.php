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

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resource controller configuration factory.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ConfigurationFactory
{
    /**
     * Current request.
     *
     * @var Request
     */
    protected $parametersParser;

    /**
     * Constructor.
     *
     * @param ParametersParser $parametersParser
     */
    public function __construct(ParametersParser $parametersParser)
    {
        $this->parametersParser = $parametersParser;
    }

    /**
     * Create configuration for given parameters.
     *
     * @param string $bundlePrefix
     * @param string $resourceName
     * @param string $templateNamespace
     * @param string $templatingEngine
     *
     * @return Configuration
     */
    public function createConfiguration($bundlePrefix, $resourceName, $templateNamespace, $templatingEngine = 'twig')
    {
        return new Configuration($this->parametersParser, $bundlePrefix, $resourceName, $templateNamespace, $templatingEngine);
    }
}
