<?php

namespace Smile\Component\Scope;


interface ScopeInterface
{
    /**
     * Get parent store if any
     * @return ScopeInterface | null
     */
    public function getParent();

    /**
     * Set store parent
     * @param ScopeInterface $scope
     * @return self
     */
    public function setParent(ScopeInterface $scope);

    /**
     * Get scope code
     * @return string
     */
    public function getCode();

    /**
     * Set scope code
     * @param string $code
     * @return self
     */
    public function setCode($code);
}