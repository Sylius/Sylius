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

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request fetcher.
 *
 * This service knows how to fetch resource handling
 * configuration from the request object.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class RequestFetcher
{
    protected $request;
    protected $configuration;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->configuration = $request->attributes->get('_sylius.resource', $this->getDefaultConfiguration());
    }

    public function getCriteria()
    {
        return $this->get('criteria', array());
    }

    public function getSorting()
    {
        return $this->get('sorting', array());
    }

    public function getRedirect()
    {
        return $this->get('redirect');
    }

    public function getFormType()
    {
        return $this->get('form');
    }

    public function getIdentifierCriteria()
    {
        return array(
            $this->getIdentifierName() => $this->getIdentifierValue()
        );
    }

    public function getIdentifierName()
    {
        return $this->get('identifier', 'id');
    }

    public function getIdentifierValue()
    {
        return $this->request->get($this->getIdentifierName());
    }

    public function isCollectionPaginated()
    {
        return (Boolean) $this->get('paginate', true);
    }

    public function getPaginationMaxPerPage()
    {
        if (!$this->isCollectionPaginated()) {
            throw new \BadMethodCallException('The current request configuration does not paginate the resources');
        }

        return $this->get('paginate', 10);
    }

    public function getTemplate()
    {
        return $this->get('template');
    }

    public function isHtmlRequest()
    {
        return 'html' === $this->request->getRequestFormat();
    }

    protected function get($name, $default = null)
    {
        return isset($this->configuration[$name]) ? $this->configuration[$name] : $default;
    }

    protected function getDefaultConfiguration()
    {
        return array();
    }
}
