<?php

namespace Smile\Component\Scope;


/**
 * Describe a value living in a particular scope
 */
interface ScopedValueInterface
{
    /**
     * Get the associated scope aware object
     *
     * @return ScopeAwareInterface
     */
    public function getScopeAware();

    /**
     * Set the associated scope aware object
     *
     * @param ScopeAwareInterface $scopeAware
     * @return self
     */
    public function setScopeAware(ScopeAwareInterface $scopeAware);

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope();

    /**
     * Set scope
     * @param string $scope
     * @return self
     */
    public function setScope($scope);
}