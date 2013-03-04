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

use FOS\RestBundle\Util\Pluralization;
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
    protected $pluralResourceName;
    protected $templateNamespace;
    protected $parameters;

    /**
     * @var Request
     */
    protected $request;

    public function __construct($bundlePrefix, $resourceName, $templateNamespace = null)
    {
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->pluralResourceName = Pluralization::pluralize($resourceName);
        $this->templateNamespace = $templateNamespace;
        $this->parameters = array();
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
        return $this->pluralResourceName;
    }

    public function getTemplateNamespace()
    {
        return $this->templateNamespace;
    }

    public function setRequest(Request $request)
    {
        $this->parameters = $request->attributes->get('_sylius');
        $this->request = $request;
    }

    public function isHtmlRequest()
    {
        if (null === $this->request) {
            throw new \BadMethodCallException('Request is unknown, cannot check its format');
        }

        return 'html' === $this->request->getRequestFormat();
    }

    public function isApiRequest()
    {
        return !$this->isHtmlRequest();
    }

    public function getServiceName($service)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $service, $this->resourceName);
    }

    public function getEventName($event)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $event);
    }

    public function getIdentifierName()
    {
        return $this->get('_identifier', 'id');
    }

    public function getIdentifierValue()
    {
        return $this->request->get($this->getIdentifierName());
    }

    public function getIdentifierCriteria()
    {
        if (null === $this->request) {
            throw new \BadMethodCallException('Request is unknown, cannot get single resource criteria');
        }

        return array(
            $this->getIdentifierName() => $this->getIdentifierValue()
        );
    }

    public function getTemplate()
    {
        return $this->get('template');
    }

    public function getFormType()
    {
        return $this->get('form', $this->getDefaultFormType());
    }

    public function getDefaultFormType()
    {
        return sprintf('%s_%s', $this->bundlePrefix, $this->resourceName);
    }

    public function getRedirectRoute()
    {
        $redirect = $this->get('redirect');

        if (is_array($redirect)) {
            return $redirect['route'];
        }

        return $redirect;
    }

    public function getRedirectParameters()
    {
        $redirect = $this->get('redirect');

        if (!is_array($redirect)) {
            return array();
        }

        foreach ($redirect['parameters'] as $key => $parameter) {
            if ('$' === $parameter[0]) {
                $redirect['parameters'][$key] = $this->request->get(str_replace('$', '', $redirect['parameters'][$key]));
            }
        }

        return $redirect['parameters'];
    }

    public function isPaginated()
    {
        return (Boolean) $this->get('paginate', true);
    }

    public function getPaginationMaxPerPage()
    {
        return (int) $this->get('paginate', 10);
    }

    public function getLimit()
    {
        return (int) $this->get('limit', 10);
    }

    public function isSortable()
    {
        return (Boolean) $this->get('sortable', false);
    }

    public function isFilterable()
    {
        return (Boolean) $this->get('filterable', false);
    }

    public function getCriteria()
    {
        $defaultCriteria = $this->get('criteria', array());

        if ($this->isFilterable() && null !== $this->request) {
            return $this->request->get('criteria', $defaultCriteria);
        }

        return $defaultCriteria;
    }

    public function getSorting()
    {
        $defaultSorting = $this->get('sorting', array());

        if ($this->isSortable() && null !== $this->request) {
            return $this->request->get('sorting', $defaultSorting);
        }

        return $defaultSorting;
    }

    public function getFlashMessage()
    {
        return $this->get('flash');
    }

    public function getRoute()
    {
        return sprintf('%s_%s', $this->bundlePrefix, $this->resourceName);
    }

    public function getIndexRoute()
    {
        return sprintf('%s_%s', $this->bundlePrefix, $this->pluralResourceName);
    }

    public function getEngineParameterName()
    {
        return sprintf('%s.engine', $this->getBundlePrefix());
    }

    protected function get($parameter, $default = null)
    {
        if (null === $this->request) {
            return $default;
        }

        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] :  $this->request->attributes->get($parameter, $default);
    }
}
