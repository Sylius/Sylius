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
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Resource controller configuration.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class RequestConfiguration
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ResourceMetadataInterface
     */
    protected $metadata;

    /**
     * @var Parameters
     */
    protected $parameters;

    /**
     * @param ResourceMetadataInterface $metadata
     * @param Request $request
     * @param Parameters $parameters
     */
    public function __construct(
        ResourceMetadataInterface $metadata,
        Request $request,
        Parameters $parameters
    ) {
        $this->metadata = $metadata;
        $this->request = $request;
        $this->parameters = $parameters;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isHtmlRequest()
    {
        return 'html' === $this->request->getRequestFormat();
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getDefaultTemplate($name)
    {
        return sprintf('%s:%s.%s', $this->metadata->getParameter('templates', ':'), $name, 'twig');
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getTemplate($name)
    {
        $template = $this->parameters->get('template', $this->getDefaultTemplate($name));

        if (null === $template) {
            throw new \RuntimeException(sprintf('Could not resolve default template for resources "%s".', $this->metadata->getAlias()));
        }

        return $template;
    }

    /**
     * @return mixed|null
     */
    public function getFormType()
    {
        return $this->parameters->get('form', sprintf('%s_%s', $this->metadata->getApplicationName(), $this->metadata->getResourceName()));
    }

    /**
     * @param $name
     * @return string
     */
    public function getRouteName($name)
    {
        return sprintf('%s_%s_%s', $this->metadata->getApplicationName(), $this->metadata->getResourceName(), $name);
    }

    /**
     * @param $name
     *
     * @return mixed|null|string
     */
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
            $parameters = $this->parseResourceValues($parameters, $resource);
        }

        return $parameters;
    }

    /**
     * @return bool
     */
    public function isLimited()
    {
        return (bool) $this->parameters->get('limit', false);
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        $limit = null;

        if ($this->isLimited()) {
            $limit = (int) $this->parameters->get('limit', 10);
        }

        return $limit;
    }

    /**
     * @return bool
     */
    public function isPaginated()
    {
        return (bool) $this->parameters->get('paginate', true);
    }

    /**
     * @return int
     */
    public function getPaginationMaxPerPage()
    {
        return (int) $this->parameters->get('paginate', 10);
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return (bool) $this->parameters->get('filterable', false);
    }

    /**
     * @param array $criteria
     * @return array
     */
    public function getCriteria(array $criteria = array())
    {
        $defaultCriteria = array_merge($this->parameters->get('criteria', array()), $criteria);

        if ($this->isFilterable()) {
            return $this->getRequestParameter('criteria', $defaultCriteria);
        }

        return $defaultCriteria;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return (bool) $this->parameters->get('sortable', true);
    }

    /**
     * @param array $sorting
     * @return array
     */
    public function getSorting(array $sorting = array())
    {
        $defaultSorting = array_merge($this->parameters->get('sorting', array()), $sorting);

        if ($this->isSortable()) {
            $sorting = $this->getRequestParameter('sorting');
            foreach ($defaultSorting as $key => $value) {
                //do not override request parameters by $defaultSorting values
                if (!isset($sorting[$key])){
                    $sorting[$key] = $value;
                }
            }

            return $sorting;
        }

        return $defaultSorting;
    }

    /**
     * @param $parameter
     * @param array $defaults
     * @return array
     */
    public function getRequestParameter($parameter, $defaults = array())
    {
        return array_replace_recursive(
            $defaults,
            $this->request->get($parameter, array())
        );
    }

    /**
     * @param $default
     * @return mixed|null
     */
    public function getRepositoryMethod($default)
    {
        $repository = $this->parameters->get('repository', array('method' => $default));

        return is_array($repository) ? $repository['method'] : $repository;
    }

    /**
     * @param array $default
     * @return array
     */
    public function getRepositoryArguments(array $default = array())
    {
        $repository = $this->parameters->get('repository', array());

        return isset($repository['arguments']) ? $repository['arguments'] : $default;
    }

    /**
     * @param $default
     * @return mixed|null
     */
    public function getFactoryMethod($default)
    {
        $factory = $this->parameters->get('factory', array('method' => $default));

        return is_array($factory) ? $factory['method'] : $factory;
    }

    /**
     * @param array $default
     * @return array
     */
    public function getFactoryArguments(array $default = array())
    {
        $factory = $this->parameters->get('factory', array());

        return isset($factory['arguments']) ? $factory['arguments'] : $default;
    }

    /**
     * @param null $message
     * @return mixed|null
     */
    public function getFlashMessage($message = null)
    {
        return $this->parameters->get('flash', $message);
    }

    /**
     * @return mixed|null
     */
    public function getSortablePosition()
    {
        return $this->parameters->get('sortable_position', 'position');
    }

    /**
     * @return mixed|null
     */
    public function getSerializationGroups()
    {
        return $this->parameters->get('serialization_groups', array());
    }

    /**
     * @return mixed|null
     */
    public function getSerializationVersion()
    {
        return $this->parameters->get('serialization_version');
    }

    /**
     * @param null $default
     * @return mixed|null
     */
    public function getEvent($default = null)
    {
        return $this->parameters->get('event', $default);
    }

    /**
     * @param null $default
     * @return mixed|null
     */
    public function getPermission($default = null)
    {
        return $this->parameters->get('permission', $default);
    }

    /**
     * @return bool
     */
    public function isHeaderRedirection()
    {
        $redirect = $this->parameters->get('redirect');

        if (!is_array($redirect) || !isset($redirect['header'])) {
            return false;
        }

        if ('xhr' === $redirect['header']) {
            return $this->getRequest()->isXmlHttpRequest();
        }

        return (bool)$redirect['header'];
    }

    /**
     * @param array  $parameters
     * @param object $resource
     *
     * @return array
     */
    private function parseResourceValues(array $parameters, $resource)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        if (empty($parameters)) {
            return array('id' => $accessor->getValue($resource, 'id'));
        }

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->parseResourceValues($value, $resource);
            }

            if (is_string($value) && 0 === strpos($value, 'resource.')) {
                $parameters[$key] = $accessor->getValue($resource, substr($value, 9));
            }
        }

        return $parameters;
    }
}
