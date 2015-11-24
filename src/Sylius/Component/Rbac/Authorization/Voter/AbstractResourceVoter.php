<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Authorization\Voter;

use Sylius\Component\Rbac\Model\IdentityInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
abstract class AbstractResourceRbacVoter implements ResourceVoterInterface
{
    /**
     * @var string
     */
    protected $resource;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @var string
     */
    protected $dataClass;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructor.
     *
     * @param string $resource
     * @param array  $actions
     * @param string $dataClass
     * @param string $prefix
     */
    public function __construct($resource, array $actions, $dataClass, $prefix = 'sylius')
    {
        $this->resource = $resource;
        $this->actions = $actions;
        $this->dataClass = $dataClass;
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($permissionCode, $resource)
    {
        if ($this->prefix !== $this->getPart($permissionCode, 'prefix')) {
            return false;
        }
        if ($this->resource !== $this->getPart($permissionCode, 'resource')) {
            return false;
        }
        if (!in_array($this->getPart($permissionCode, 'action'), $this->actions)) {
            return false;
        }

        return in_array($this->dataClass, class_implements($resource));
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(IdentityInterface $identity, $permissionCode, $resource)
    {
        $action = $this->getPart($permissionCode, 'action');

        return $this->isActionGranted($identity, $action, $resource);
    }

    /**
     * Implement your own resource specific logic here.
     *
     * @param IdentityInterface $identity
     * @param string            $action
     * @param mixed             $resource
     *
     * @return bool
     */
    abstract protected function isActionGranted(IdentityInterface $identity, $action, $resource);

    /**
     * Get application/bundle prefix, resource name and action from permission code.
     *
     * @param string $permissionCode
     * @param string $component      One of 'prefix', 'resource', 'action'
     *
     * @return string
     */
    protected function getPart($permissionCode, $component)
    {
        $components = array_combine(array('prefix', 'resource', 'action'), explode('.', $permissionCode));

        return $components[$component];
    }
}
