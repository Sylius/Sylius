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
    protected $bundlePrefix;
    protected $resourceName;
    protected $templateNamespace;
    protected $templatingEngine;
    protected $parameters;

    /**
     * Current request.
     *
     * @var Request
     */
    protected $request;

    public function __construct($bundlePrefix, $resourceName, $templateNamespace, $templatingEngine = 'twig')
    {

        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->templateNamespace = $templateNamespace;
        $this->templatingEngine = $templatingEngine;

        $this->parameters = array();
    }

    public function load(Request $request)
    {
        $this->request = $request;

        $parameters = $request->attributes->get('_sylius', array());
        $parser = new ParametersParser();

        $parameters = $parser->parse($parameters, $request);

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
        return 'html' !== $this->request->getRequestFormat();
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

    public function getRedirectParameters()
    {
        $redirect = $this->get('redirect');

        if (null === $redirect || !is_array($redirect)) {
            return array();
        }

        return $redirect['parameters'];
    }

    public function getLimit()
    {
        return (int) $this->get('limit', 10);
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

    public function getCriteria()
    {
        $defaultCriteria = $this->get('criteria', array());

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

    public function getFlashMessage($message = null)
    {
        $message = sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $message);
        return $this->get('flash', $message);
    }

    protected function get($parameter, $default = null)
    {
        return array_key_exists($parameter, $this->parameters) ? $this->parameters[$parameter] : $default;
    }
}
