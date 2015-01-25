<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Scope\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Scope\ScopedValueInterface;
use Sylius\Component\Scope\ScopeInterface;

/**
 * Add ScopeAware behaviour to an entity
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
trait ScopeAwareTrait
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $scopedValues;

    /**
     * @var ScopeInterface
     */
    protected $currentScope;

    /**
     * @var ScopedValueInterface
     */
    protected $currentScopedValue;

    /**
     * Init ArrayCollection
     */
    protected function initScopeAware()
    {
        $this->scopedValues = new ArrayCollection();
    }

    /**
     * Get all scoped values
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getScopedValues()
    {
        return $this->scopedValues;
    }

    /**
     * Add a new scoped value
     *
     * @param ScopedValueInterface $scopedValue
     * @return self
     */
    public function addScopedValue(ScopedValueInterface $scopedValue)
    {
        if (!$this->scopedValues->contains($scopedValue)) {
            $this->scopedValues[] = $scopedValue;
            $scopedValue->setScopeAware($this);
        }

        return $this;
    }

    /**
     * Remove a scoped value
     *
     * @param ScopedValueInterface $scopedValue
     * @return self
     */
    public function removeScopedValue(ScopedValueInterface $scopedValue)
    {
        if ($this->scopedValues->removeElement($scopedValue)) {
            $scopedValue->setScopeAware(null);
        }

        return $this;
    }

    /**
     * Set current scope
     *
     * @param ScopeInterface $currentScope
     * @return self
     */
    public function setCurrentScope(ScopeInterface $currentScope = null)
    {
        $this->currentScope = $currentScope;

        return $this;
    }

    /**
     * Get current scope
     *
     * @return ScopeInterface|null
     */
    public function getCurrentScope()
    {
        return $this->currentScope;
    }

    /**
     * Scope helper method
     *
     * @param ScopeInterface $scope
     * @return ScopedValueInterface
     */
    public function scope($scope = null)
    {
        if (null === $scope) {
            $scope = $this->currentScope;
        }

        if (!$scope) {
            throw new \RuntimeException('No scope has been set and currentScope is empty');
        }

        if ($this->currentScopedValue && $this->currentScopedValue->getScope() === $scope) {
            return $this->currentScopedValue;
        }

        /** @var ScopedValueInterface $scopedValue */
        if (!$scopedValue = $this->scopedValues->get($scope->getCode())) {
            if ($scope->getParent()) {
                return $this->scope($scope->getParent());
            } else {
                $className = $this->getScopedEntityClass();
                $scopedValue = new $className();
                $scopedValue->setScope($scope->getCode());
                $this->addScopedValue($scopedValue);
            }
        }

        $this->currentScopedValue = $scopedValue;

        return $scopedValue;
    }

    protected function getScopedEntityClass()
    {
        return get_class($this) . 'Scoped';
    }
}