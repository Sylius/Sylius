<?php

namespace Sylius\Bundle\RbacBundle\Security\Role;

class Inflector implements InflectorInterface
{
    const DEFAULT_PREFIX = 'ROLE_';

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct($prefix = self::DEFAULT_PREFIX)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function toSecurityRole($name)
    {
        if (0 !== strpos($name, $this->prefix)) {
            $name = $this->prefix.$name;
        }

        return str_replace('.', '_', strtoupper($name));
    }

    /**
     * {@inheritdoc}
     */
    public function toRbacRole($name)
    {
        if (substr($name, 0, strlen($this->prefix)) == $this->prefix) {
            $name = substr($name, strlen($this->prefix));
        }

        return str_replace('_', '.', strtolower($name));
    }
}
