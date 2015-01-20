<?php


namespace Smile\Component\Scope\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Smile\Component\Scope\ScopedValueInterface;
use Smile\Component\Scope\ScopeInterface;

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