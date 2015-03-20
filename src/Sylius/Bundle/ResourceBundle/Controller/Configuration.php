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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class Configuration
{
    /**
     * @var ParametersParser
     */
    protected $parser;

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
     * @var Parameters
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $settings = array();

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
        $templatingEngine = 'twig',
        array $settings
    ) {
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->templateNamespace = $templateNamespace;
        $this->templatingEngine = $templatingEngine;
        $this->settings = $settings;
        $this->parser = $parser;
        $this->parameters = new Parameters();
    }

    /**
     * @return Parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(Parameters $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
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
        return $this->parameters->get('template', $this->getTemplateName($name));
    }

    public function getFormType()
    {
        return $this->parameters->get('form', sprintf('%s_%s', $this->bundlePrefix, $this->resourceName));
    }

    public function getRouteName($name)
    {
        return sprintf('%s_%s_%s', $this->bundlePrefix, $this->resourceName, $name);
    }

    public function getRedirectRoute($name)
    {
        $redirect = $this->parameters->get('redirect');

        if (null === $redirect) {
            return $this->getRouteName($name);
        }

        if (is_array($redirect)) {
            if (!empty($redirect['referer'])) {
                return 'referer';
            }

            return $redirect['route'];
        }

        return $redirect;
    }

    /**
     * Get url hash fragment (#text) which is you configured.
     *
     * @return null|string
     */
    public function getRedirectHash()
    {
        $redirect = $this->parameters->get('redirect');

        if (!is_array($redirect) || empty($redirect['hash'])) {
            return null;
        }

        return '#'.$redirect['hash'];
    }

    /**
     * Get redirect referer, This will detected by configuration
     * If not exists, The `referrer` from headers will be used.
     *
     * @return null|string
     */
    public function getRedirectReferer()
    {
        $redirect = $this->parameters->get('redirect');
        $referer = $this->request->headers->get('referer');

        if (!is_array($redirect) || empty($redirect['referer'])) {
            return $referer;
        }

        if ($redirect['referer'] === true) {
            return $referer;
        }

        return $redirect['referer'];
    }

    /**
     * @param object|null $resource
     *
     * @return array
     */
    public function getRedirectParameters($resource = null)
    {
        $redirect = $this->parameters->get('redirect');

        if (!is_array($redirect) || empty($redirect['parameters'])) {
            $redirect = array('parameters' => array());
        }

        $parameters = $redirect['parameters'];

        if (null !== $resource) {
            $parameters = $this->parser->process($parameters, $resource);
        }

        return $parameters;
    }

    public function isLimited()
    {
        return (bool) $this->parameters->get('limit', $this->settings['limit']);
    }

    public function getLimit()
    {
        $limit = null;
        if ($this->isLimited()) {
            $limit = (int) $this->parameters->get('limit', $this->settings['limit']);
        }

        return $limit;
    }

    public function isPaginated()
    {
        return (bool) $this->parameters->get('paginate', $this->settings['default_page_size']);
    }

    public function getPaginationMaxPerPage()
    {
        return (int) $this->parameters->get('paginate', $this->settings['default_page_size']);
    }

    public function isFilterable()
    {
        return (bool) $this->parameters->get('filterable', $this->settings['filterable']);
    }

    public function getCriteria(array $criteria = array())
    {
        $defaultCriteria = array_merge($this->parameters->get('criteria', array()), $criteria);

        if ($this->isFilterable()) {
            return $this->getRequestParameter('criteria', $defaultCriteria);
        }

        return $defaultCriteria;
    }

    public function isSortable()
    {
        return (bool) $this->parameters->get('sortable', $this->settings['sortable']);
    }

    public function getSorting(array $sorting = array())
    {
        $defaultSorting = array_merge($this->parameters->get('sorting', array()), $sorting);

        if ($this->isSortable()) {
            return $this->getRequestParameter('sorting', $defaultSorting);
        }

        return $defaultSorting;
    }

    public function getRequestParameter($parameter, $defaults = array())
    {
        return array_replace_recursive(
            $defaults,
            $this->request->get($parameter, array())
        );
    }

    public function getRepositoryMethod($default)
    {
        $repository = $this->parameters->get('repository', array('method' => $default));

        return is_array($repository) ? $repository['method'] : $repository;
    }

    public function getRepositoryArguments(array $default = array())
    {
        $repository = $this->parameters->get('repository', array());

        return isset($repository['arguments']) ? $repository['arguments'] : $default;
    }

    public function getFactoryMethod($default)
    {
        $factory = $this->parameters->get('factory', array('method' => $default));

        return is_array($factory) ? $factory['method'] : $factory;
    }

    public function getFactoryArguments(array $default = array())
    {
        $factory = $this->parameters->get('factory', array());

        return isset($factory['arguments']) ? $factory['arguments'] : $default;
    }

    public function getFlashMessage($message = null)
    {
        $message = sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $message);

        return $this->parameters->get('flash', $message);
    }

    public function getSortablePosition()
    {
        return $this->parameters->get('sortable_position', 'position');
    }

    public function getSerializationGroups()
    {
        return $this->parameters->get('serialization_groups', array());
    }

    public function getSerializationVersion()
    {
        return $this->parameters->get('serialization_version');
    }

    public function getEvent($default = null)
    {
        return $this->parameters->get('event', $default);
    }

    public function getPermission($default = null)
    {
        return $this->parameters->get('permission', $default);
    }
}
