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
use Symfony\Component\PropertyAccess\PropertyAccess;

class RequestConfiguration
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var Parameters
     */
    private $parameters;

    public function __construct(MetadataInterface $metadata, Request $request, Parameters $parameters)
    {
        $this->metadata = $metadata;
        $this->request = $request;
        $this->parameters = $parameters;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @return Parameters
     */
    public function getParameters(): Parameters
    {
        return $this->parameters;
    }

    public function getSection(): ?string
    {
        return $this->parameters->get('section');
    }

    public function isHtmlRequest(): bool
    {
        return 'html' === $this->request->getRequestFormat();
    }

    public function getDefaultTemplate($name): ?string
    {
        $templatesNamespace = (string) $this->metadata->getTemplatesNamespace();

        if (false !== strpos($templatesNamespace, ':')) {
            return sprintf('%s:%s.%s', $templatesNamespace ?: ':', $name, 'twig');
        }

        return sprintf('%s/%s.%s', $templatesNamespace, $name, 'twig');
    }

    /**
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

    public function getFormType(): ?string
    {
        $form = $this->parameters->get('form');
        if (isset($form['type'])) {
            return $form['type'];
        }

        if (is_string($form)) {
            return $form;
        }

        $form = $this->metadata->getClass('form');
        if (is_string($form)) {
            return $form;
        }

        return sprintf('%s_%s', $this->metadata->getApplicationName(), $this->metadata->getName());
    }

    public function getFormOptions(): array
    {
        $form = $this->parameters->get('form');
        if (isset($form['options'])) {
            return $form['options'];
        }

        return [];
    }

    public function getRouteName($name): string
    {
        $sectionPrefix = $this->getSection() ? $this->getSection() . '_' : '';

        return sprintf('%s_%s%s_%s', $this->metadata->getApplicationName(), $sectionPrefix, $this->metadata->getName(), $name);
    }

    /**
     * @return mixed|string|null
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
     */
    public function getRedirectHash(): string
    {
        $redirect = $this->parameters->get('redirect');

        if (!is_array($redirect) || empty($redirect['hash'])) {
            return '';
        }

        return '#' . $redirect['hash'];
    }

    /**
     * Get redirect referer, This will detected by configuration
     * If not exists, The `referrer` from headers will be used.
     */
    public function getRedirectReferer(): string
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
     */
    public function getRedirectParameters($resource = null): array
    {
        $redirect = $this->parameters->get('redirect');

        if ($this->areParametersIntentionallyEmptyArray($redirect)) {
            return [];
        }

        if (!is_array($redirect)) {
            $redirect = ['parameters' => []];
        }

        $parameters = $redirect['parameters'] ?? [];
        $parameters = $this->addExtraRedirectParameters($parameters);

        if (null !== $resource) {
            $parameters = $this->parseResourceValues($parameters, $resource);
        }

        return $parameters;
    }

    private function addExtraRedirectParameters(array $parameters): array
    {
        $vars = $this->getVars();
        $accessor = PropertyAccess::createPropertyAccessor();

        if ($accessor->isReadable($vars, '[redirect][parameters]')) {
            $extraParameters = $accessor->getValue($vars, '[redirect][parameters]');

            if (is_array($extraParameters)) {
                $parameters = array_merge($parameters, $extraParameters);
            }
        }

        return $parameters;
    }

    public function isLimited(): bool
    {
        return (bool) $this->parameters->get('limit', false);
    }

    public function getLimit(): ?int
    {
        $limit = null;

        if ($this->isLimited()) {
            $limit = (int) $this->parameters->get('limit', 10);
        }

        return $limit;
    }

    public function isPaginated(): bool
    {
        $pagination = $this->parameters->get('paginate', true);

        return $pagination !== false && $pagination !== null;
    }

    public function getPaginationMaxPerPage(): int
    {
        return (int) $this->parameters->get('paginate', 10);
    }

    public function isFilterable(): bool
    {
        return (bool) $this->parameters->get('filterable', false);
    }

    public function getCriteria(array $criteria = []): array
    {
        $defaultCriteria = array_merge($this->parameters->get('criteria', []), $criteria);

        if ($this->isFilterable()) {
            return $this->getRequestParameter('criteria', $defaultCriteria);
        }

        return $defaultCriteria;
    }

    public function isSortable(): bool
    {
        return (bool) $this->parameters->get('sortable', false);
    }

    public function getSorting(array $sorting = []): array
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

    public function getRequestParameter($parameter, array $defaults = []): array
    {
        return array_replace_recursive(
            $defaults,
            $this->request->get($parameter, [])
        );
    }

    public function getRepositoryMethod(): ?string
    {
        if (!$this->parameters->has('repository')) {
            return null;
        }

        $repository = $this->parameters->get('repository');

        return is_array($repository) ? $repository['method'] : $repository;
    }

    public function getRepositoryArguments(): array
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

    public function getFactoryMethod(): ?string
    {
        if (!$this->parameters->has('factory')) {
            return null;
        }

        $factory = $this->parameters->get('factory');

        return is_array($factory) ? $factory['method'] : $factory;
    }

    public function getFactoryArguments(): array
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

    public function getEvent(): ?string
    {
        return $this->parameters->get('event');
    }

    public function hasPermission(): bool
    {
        return false !== $this->parameters->get('permission', false);
    }

    /**
     * @throws \LogicException
     */
    public function getPermission(string $name): string
    {
        $permission = $this->parameters->get('permission');

        if (null === $permission) {
            throw new \LogicException('Current action does not require any authorization.');
        }

        if (true === $permission) {
            return sprintf('%s.%s.%s', $this->metadata->getApplicationName(), $this->metadata->getName(), $name);
        }

        return $permission;
    }

    public function isHeaderRedirection(): bool
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
     * @param object $resource
     */
    private function parseResourceValues(array $parameters, $resource): array
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

    public function hasGrid(): bool
    {
        return $this->parameters->has('grid');
    }

    /**
     * @throws \LogicException
     */
    public function getGrid(): string
    {
        if (!$this->hasGrid()) {
            throw new \LogicException('Current action does not use grid.');
        }

        return $this->parameters->get('grid');
    }

    public function hasStateMachine(): bool
    {
        return $this->parameters->has('state_machine');
    }

    public function getStateMachineGraph(): string
    {
        $options = $this->parameters->get('state_machine');

        return $options['graph'] ?? null;
    }

    public function getStateMachineTransition(): string
    {
        $options = $this->parameters->get('state_machine');

        return $options['transition'] ?? null;
    }

    public function isCsrfProtectionEnabled(): bool
    {
        return $this->parameters->get('csrf_protection', true);
    }

    /**
     * @param mixed $redirect
     */
    private function areParametersIntentionallyEmptyArray($redirect): bool
    {
        return isset($redirect['parameters']) && is_array($redirect['parameters']) && empty($redirect['parameters']);
    }
}
