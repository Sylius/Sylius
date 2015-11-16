<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Metadata;

use Doctrine\Common\Inflector\Inflector;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceMetadata implements ResourceMetadataInterface
{
    /**
     * @var string
     */
    private $resourceName;

    /**
     * @var string
     */
    private $applicationName;

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $templatesNamespace;

    /**
     * @var array
     */
    private $classes = array();

    /**
     * @var bool
     */
    private $timestampable = false;

    /**
     * @var bool
     */
    private $translatable = false;

    /**
     * @var bool
     */
    private $softdeleteable = false;

    /**
     * @param string $applicationName
     * @param string $resourceName
     * @param array $parameters
     * @param string $driver
     * @param null|string $templatesNamespace
     * @param array $classes
     */
    public function __construct(
        $applicationName,
        $resourceName,
        array $parameters,
        $driver,
        $templatesNamespace = null,
        array $classes
    ) {
        $this->applicationName = $applicationName;
        $this->resourceName = $resourceName;
        $this->parameters = $parameters;
        $this->driver = $driver;
        $this->templatesNamespace = $templatesNamespace;
        $this->classes = $this->flattenArray($classes);

        $reflection = new \ReflectionClass($classes['model']);
        $translatableInterface = 'Sylius\Component\Translation\Model\TranslatableInterface';

        $this->translatable = interface_exists($translatableInterface) && $reflection->implementsInterface($translatableInterface);
    }

    /**
     * @param string $alias
     * @param array  $configuration
     */
    public static function fromConfigurationArray($alias, array $configuration)
    {
        list($applicationName, $resourceName) = explode('.', $alias);

        return new self(
            $applicationName,
            $resourceName,
            $configuration,
            $configuration['driver'],
            array_key_exists('templates', $configuration) ? $configuration['templates'] : null,
            $configuration['classes']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPluralResourceName()
    {
        return Inflector::pluralize($this->resourceName);
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->getApplicationName().'.'.$this->getResourceName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplatesNamespace()
    {
        return $this->templatesNamespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($type, $default = null)
    {
        if (!$this->hasParameter($type)) {
            if (null === $default) {
                throw new \InvalidArgumentException(sprintf(
                    'Resource "%s" does not have a configured parameter "%s".',
                    $this->getResourceName(),
                    $type
                ));
            }

            return $default;
        }

        return $this->parameters[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($type)
    {
        return array_key_exists($type, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass($type)
    {
        if (!$this->hasClass($type)) {
            print_r($this->classes);
            throw new \InvalidArgumentException(sprintf(
                'Resource "%s" does not have a configured class for "%s".',
                $this->getResourceName(),
                $type
            ));
        }

        return $this->classes[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function hasClass($type)
    {
        return array_key_exists($type, $this->classes);
    }

    /**
     * {@inheritdoc}
     */
    public function isTimestampable()
    {
        return $this->timestampable;
    }

    /**
     * {@inheritdoc}
     */
    public function isTranslatable()
    {
        return $this->translatable;
    }

    /**
     * {@inheritdoc}
     */
    public function isSoftdeleteable()
    {
        return $this->softdeleteable;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceId($service)
    {
        return sprintf('%s.%s.%s', $this->applicationName, $service, $this->resourceName);
    }

    /**
     * @param array $array
     * @param string $prefix;
     *
     * @return array
     */
    private function flattenArray(array $array, $prefix = null)
    {
        $flattenedArray = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattenedArray = array_merge($flattenedArray, $this->flattenArray($value, $key.'.'));
            } else {
                $flattenedArray[$prefix.$key] = $value;
            }
        }

        return $flattenedArray;
    }
}
