<?php

namespace Smile\Component\Scope\Entity;


use Doctrine\ORM\Mapping as ORM;
use Smile\Component\Scope\Doctrine\Annotation as Smile;
use Smile\Component\Scope\ScopeAwareInterface;

trait ScopedValueTrait
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ScopeAwareInterface
     */
    protected $scopeAware;

    /**
     * @ORM\Column(name="scope", type="string")
     * @Smile\Scope
     * @var string
     */
    protected $scope;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get associated scope aware object
     *
     * @return ScopeAwareInterface
     */
    public function getScopeAware()
    {
        return $this->scopeAware;
    }

    /**
     * Set the associated scope aware object
     *
     * @param ScopeAwareInterface $scopeAware
     * @return self
     */
    public function setScopeAware(ScopeAwareInterface $scopeAware = null)
    {
        if ($this->scopeAware != $scopeAware) {
            $old = $this->scopeAware;
            $this->scopeAware = $scopeAware;

            if ($old !== null) {
                $old->removeScopedValue($this);
            }

            if ($scopeAware !== null) {
                $scopeAware->addScopedValue($this);
            }
        }

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return self
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }
}