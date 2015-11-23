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
    protected $application;

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
     * Constructor.
     *
     * @param string $application
     * @param string $resource
     * @param array  $actions
     * @param string $dataClass
     */
    public function __construct($application, $resource, array $actions, $dataClass)
    {
        $this->application = $application;
        $this->resource = $resource;
        $this->actions = $actions;
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($permissionCode, $resource)
    {
        if ($this->application !== $this->getComponent($permissionCode, 'application')) {
            return false;
        }
        if ($this->resource !== $this->getComponent($permissionCode, 'resource')) {
            return false;
        }
        if (!in_array($this->getComponent($permissionCode, 'action'), $this->actions)) {
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
        $action = $this->getComponent($permissionCode, 'action');

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

    private function getComponent($permissionCode, $component)
    {
        $components = array_combine(array('application', 'resource', 'action'), explode('.', $permissionCode));

        return $components[$component];
    }
}
