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

use Sylius\Component\Resource\Metadata\MetadataInterface;
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
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var Parameters
     */
    protected $parameters;

    /**
     * @param MetadataInterface $metadata
     * @param Request $request
     * @param Parameters $parameters
     */
    public function __construct(MetadataInterface $metadata, Request $request, Parameters $parameters)
    {
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
     * @return MetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return Parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string|null
     */
    public function getSection()
    {
        return $this->parameters->get('section');
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
     *
     * @return null|string
     */
    public function getDefaultTemplate($name)
    {
        $templatesNamespace = $this->metadata->getTemplatesNamespace();

        if (false !== strpos($templatesNamespace, ':')) {
            return sprintf('%s:%s.%s', $templatesNamespace ?: ':', $name, 'twig');
        }

        return sprintf('%s/%s.%s', $templatesNamespace, $name, 'twig');
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getTemplate($name)
    {
        $template = $this->parameters->get('template', $this->getDefaultTemplate($name));

        if (null === $template) {
            throw new \RuntimeException(sprintf('Could not resolve template for resource "%s".', $this->metadata->getAlias()));
        }

        return $template;
    }

    /**
     * @return mixed|null
     */
    public function getFormType()
    {
        $form = $this->parameters->get('form', sprintf('%s_%s', $this->metadata->getApplicationName(), $this->metadata->getName()));

        if (is_array($form) && array_key_exists('type', $form)) {
            return $form['type'];
        }

        return $form;
    }

    /**
     * @return mixed|null
     */
    public function getFormOptions()
    {
        $form = $this->parameters->get('form', sprintf('%s_%s', $this->metadata->getApplicationName(), $this->metadata->getName()));

        if (is_array($form) && array_key_exists('options', $form)) {
            return $form['options'];
        }

        return [];
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function getRouteName($name)
    {
        $sectionPrefix = $this->getSection() ? $this->getSection().'_' : '';

        return sprintf('%s_%s%s_%s', $this->metadata->getApplicationName(), $sectionPrefix, $this->metadata->getName(), $name);
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

        if ($this->areParametersIntentionallyEmptyArray($redirect)) {
            return [];
        }

        if (!is_array($redirect)) {
            $redirect = ['parameters' => []];
        }

        $parameters = isset($redirect['parameters']) ? $redirect['parameters'] : [];

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
     *
     * @return array
     */
    public function getCriteria(array $criteria = [])
    {
        $defaultCriteria = array_merge($this->parameters->get('criteria', []), $criteria);

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
        return (bool) $this->parameters->get('sortable', false);
    }

    /**
     * @param array $sorting
     *
     * @return array
     */
    public function getSorting(array $sorting = [])
    {
        $defaultSorting = array_merge($this->parameters->get('sorting', []), $sorting);

        if ($this->isSortable()) {
            $sorting = $this->getRequestParameter('sorting');
            foreach ($defaultSorting as $key => $value) {
                if (!isset($sorting[$key])) {
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
     *
     * @return array
     */
    public function getRequestParameter($parameter, $defaults = [])
    {
        return array_replace_recursive(
            $defaults,
            $this->request->get($parameter, [])
        );
    }

    /**
     * @return string|null
     */
    public function getRepositoryMethod()
    {
        if (!$this->parameters->has('repository')) {
            return null;
        }

        $repository = $this->parameters->get('repository');

        return is_array($repository) ? $repository['method'] : $repository;
    }

    /**
     * @return array
     */
    public function getRepositoryArguments()
    {
        if (!$this->parameters->has('repository')) {
            return [];
        }

        $repository = $this->parameters->get('repository');

        if (!isset($repository['arguments'])) {
            return [];
        }

        return is_array($repository['arguments']) ? $repository['arguments'] : [$repository['arguments']];
    }

    /**
     * @return string|null
     */
    public function getFactoryMethod()
    {
        if (!$this->parameters->has('factory')) {
            return null;
        }

        $factory = $this->parameters->get('factory');

        return is_array($factory) ? $factory['method'] : $factory;
    }

    /**
     * @return array
     */
    public function getFactoryArguments()
    {
        if (!$this->parameters->has('factory')) {
            return [];
        }

        $factory = $this->parameters->get('factory');

        if (!isset($factory['arguments'])) {
            return [];
        }

        return is_array($factory['arguments']) ? $factory['arguments'] : [$factory['arguments']];
    }

    /**
     * @param null $message
     *
     * @return mixed|null
     */
    public function getFlashMessage($message)
    {
        return $this->parameters->get('flash', sprintf('%s.%s.%s', $this->metadata->getApplicationName(), $this->metadata->getName(), $message));
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
        return $this->parameters->get('serialization_groups', []);
    }

    /**
     * @return mixed|null
     */
    public function getSerializationVersion()
    {
        return $this->parameters->get('serialization_version');
    }

    /**
     * @return string|null
     */
    public function getEvent()
    {
        return $this->parameters->get('event');
    }

    /**
     * @return bool
     */
    public function hasPermission()
    {
        return false !== $this->parameters->get('permission', false);
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \LogicException
     */
    public function getPermission($name)
    {
        if (!$this->hasPermission()) {
            throw new \LogicException('Current action does not require any authorization.');
        }

        $permission = $this->parameters->get('permission');

        if ($permission === true) {
            return sprintf('%s.%s.%s', $this->metadata->getApplicationName(), $this->metadata->getName(), $name);
        }

        return $permission;
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

        return (bool) $redirect['header'];
    }

    public function getVars()
    {
        return $this->parameters->get('vars', []);
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
            return ['id' => $accessor->getValue($resource, 'id')];
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

    /**
     * @return bool
     */
    public function hasGrid()
    {
        return $this->parameters->has('grid');
    }

    /**
     * @return string
     *
     * @throws \LogicException
     */
    public function getGrid()
    {
        if (!$this->hasGrid()) {
            throw new \LogicException('Current action does not use grid.');
        }

        return $this->parameters->get('grid');
    }

    /**
     * @return bool
     */
    public function hasStateMachine()
    {
        return $this->parameters->has('state_machine');
    }

    /**
     * @return string
     */
    public function getStateMachineGraph()
    {
        return $this->parameters->get('state_machine[graph]', null, true);
    }

    /**
     * @return string
     */
    public function getStateMachineTransition()
    {
        return $this->parameters->get('state_machine[transition]', null, true);
    }

    /**
     * @param mixed $redirect
     *
     * @return bool
     */
    private function areParametersIntentionallyEmptyArray($redirect)
    {
        return isset($redirect['parameters']) && is_array($redirect['parameters']) && empty($redirect['parameters']);
    }
}
