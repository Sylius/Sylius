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
 * Resource controller configuration.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Configuration
{
    /**
     * @var string
     */
    protected $bundlePrefix;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var string
     */
    protected $templateNamespace;

    /**
     * @var string
     */
    protected $templatingEngine;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var ParametersParser
     */
    protected $parser;

    /**
     * Current request.
     *
     * @var Request
     */
    protected $request;

    public function __construct(
        ParametersParser $parser,
        $bundlePrefix,
        $resourceName,
        $templateNamespace,
        $templatingEngine = 'twig'
    ) {
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->templateNamespace = $templateNamespace;
        $this->templatingEngine = $templatingEngine;
        $this->parser = $parser;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        $parameters = $request->attributes->get('_sylius', array());
        $this->parser->parse($parameters, $request);

        $this->parameters = $parameters;
    }

    public function getBundlePrefix()
    {
        return $this->bundlePrefix;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function getPluralResourceName()
    {
        return Inflector::pluralize($this->resourceName);
    }

    public function getTemplateNamespace()
    {
        return $this->templateNamespace;
    }

    public function getTemplatingEngine()
    {
        return $this->templatingEngine;
    }

    public function isApiRequest()
    {
        return null !== $this->request && 'html' !== $this->request->getRequestFormat();
    }

    public function getServiceName($service)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $service, $this->resourceName);
    }

    public function getEventName($event)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $event);
    }

    public function getTemplateName($name)
    {
        return sprintf('%s:%s.%s', $this->templateNamespace ?: ':', $name, $this->templatingEngine);
    }

    public function getTemplate($name)
    {
        return $this->get('template', $this->getTemplateName($name));
    }

    public function getFormType()
    {
        return $this->get('form', sprintf('%s_%s', $this->bundlePrefix, $this->resourceName));
    }

    public function getRouteName($name)
    {
        return sprintf('%s_%s_%s', $this->bundlePrefix, $this->resourceName, $name);
    }

    public function getRedirectRoute($name)
    {
        $redirect = $this->get('redirect');

        if (null === $redirect) {
            return $this->getRouteName($name);
        }

        if (is_array($redirect)) {
            return $redirect['route'];
        }

        return $redirect;
    }

    /**
     * @param object|null $resource
     *
     * @return array
     */
    public function getRedirectParameters($resource = null)
    {
        $redirect = $this->get('redirect');

        if (null === $redirect || !is_array($redirect)) {
            $redirect = array('parameters' => array());
        }

        $parameters = $redirect['parameters'];

        if (null !== $resource) {
            $parameters = $this->parser->process($parameters, $resource);
        }

        return $parameters;
    }

    public function getLimit()
    {
        $limit = $this->get('limit', 10);

        if (false === $limit) {
            return null;
        }

        return (int) $limit;
    }

    public function isPaginated()
    {
        return (Boolean) $this->get('paginate', true);
    }

    public function getPaginationMaxPerPage()
    {
        return (int) $this->get('paginate', 10);
    }

    public function isFilterable()
    {
        return (Boolean) $this->get('filterable', false);
    }

    public function getCriteria($default = array())
    {
        $defaultCriteria = array_merge($this->get('criteria', array()), $default);

        if ($this->isFilterable()) {
            return array_merge($defaultCriteria, $this->request->get('criteria', array()));
        }

        return $defaultCriteria;
    }

    public function isSortable()
    {
        return (Boolean) $this->get('sortable', false);
    }

    public function getSorting()
    {
        $defaultSorting = $this->get('sorting', array());

        if ($this->isSortable()) {
            return array_merge($defaultSorting, $this->request->get('sorting', array()));
        }

        return $defaultSorting;
    }

    public function getMethod($default)
    {
        return $this->get('method', $default);
    }

    public function getArguments(array $default = array())
    {
        return $this->get('arguments', $default);
    }

    public function getFactoryMethod($default)
    {
        $factory = $this->get('factory', array('method' => $default));

        return is_array($factory) ? $factory['method'] : $factory;
    }

    public function getFactoryArguments(array $default = array())
    {
        $factory = $this->get('factory', array());

        return isset($factory['arguments']) ? $factory['arguments'] : $default;
    }

    public function getFlashMessage($message = null)
    {
        $message = sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $message);

        return $this->get('flash', $message);
    }

    public function getSortablePosition()
    {
        return $this->get('sortable_position', 'position');
    }

    protected function get($parameter, $default = null)
    {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : $default;
    }
}
