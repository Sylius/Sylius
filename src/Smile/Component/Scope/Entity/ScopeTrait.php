<?php


namespace Smile\Component\Scope\Entity;


use Smile\Component\Scope\ScopeInterface;

trait ScopeTrait
{
    /**
     * @var string
     */
    protected $code;
    /**
     * @var ScopeInterface
     */
    protected $parent;

    /**
     * Get parent store if any
     * @return ScopeInterface | null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set store parent
     * @param ScopeInterface $scope
     * @return self
     */
    public function setParent(ScopeInterface $scope = null)
    {
        $this->parent = $scope;

        return $this;
    }

    /**
     * Get scope code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set scope code
     * @param string $code
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}