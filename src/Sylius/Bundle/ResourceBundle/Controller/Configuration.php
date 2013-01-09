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
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class Configuration
{
    protected $bundlePrefix;
    protected $resourceName;
    protected $templateNamespace;

    /**
     * @var Request
     */
    protected $request;

    public function __construct($bundlePrefix, $resourceName, $templateNamespace = null)
    {
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->templateNamespace = $templateNamespace;
    }

    public function getBundlePrefix()
    {
        return $this->bundlePrefix;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function getTemplateNamespace()
    {
        return $this->templateNamespace;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function isHtmlRequest()
    {
        if (null === $this->request) {
            throw new \BadMethodCallException('Request is unknown, cannot check its format');
        }

        return 'html' === $this->request->getRequestFormat();
    }

    public function getServiceName($service)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $service, $this->resourceName);
    }

    public function getIdentifier()
    {
        return $this->get('identifier', 'id');
    }

    public function getIdentifierValue()
    {
        return $this->request->get($this->getIdentifier());
    }

    public function getIdentifierCriteria()
    {
        if (null === $this->request) {
            throw new \BadMethodCallException('Request is unknown, cannot get single resource criteria');
        }

        return array(
            $this->getIdentifier() => $this->getIdentifierValue()
        );
    }

    public function getTemplate()
    {
        return $this->get('_template');
    }

    public function getFormType()
    {
        return $this->get('_form', $this->getDefaultFormType());
    }

    public function getDefaultFormType()
    {
        return sprintf('%s_%s', $this->bundlePrefix, $this->resourceName);
    }

    public function getRedirect()
    {
        return $this->get('_redirect');
    }

    public function isCollectionPaginated()
    {
        return (Boolean) $this->get('_paginate', true);
    }

    public function getPaginationMaxPerPage()
    {
        return (int) $this->get('_paginate', 10);
    }

    public function getCollectionLimit()
    {
        return (int) $this->get('_limit', 10);
    }

    public function isCollectionSortable()
    {
        return (Boolean) $this->get('_sortable', false);
    }

    public function isCollectionFilterable()
    {
        return (Boolean) $this->get('_filterable', false);
    }

    public function getCriteria()
    {
        $defaultCriteria = $this->get('_criteria', array());

        if ($this->isCollectionFilterable() && null !== $this->request) {
            return $this->request->get('_criteria', $defaultCriteria);
        }

        return $defaultCriteria;
    }

    public function getSorting()
    {
        $defaultSorting = $this->get('_sorting', array());

        if ($this->isCollectionSortable() && null !== $this->request) {
            return $this->request->get('_sorting', $defaultSorting);
        }

        return $defaultSorting;
    }

    public function getFlashMessage()
    {
        return $this->get('_flash');
    }

    public function getRoute()
    {
        return sprintf('%s_%s', $this->bundlePrefix, $this->resourceName);
    }

    public function getCollectionRoute()
    {
        return sprintf('%s_%s', $this->bundlePrefix, Pluralization::pluralize($this->resourceName));
    }

    protected function get($parameter, $default = null)
    {
        if (null === $this->request) {
            return $default;
        }

        return $this->request->attributes->get($parameter, $default);
    }
}
