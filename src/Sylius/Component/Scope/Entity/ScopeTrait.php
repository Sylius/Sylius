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


use Sylius\Component\Scope\ScopeInterface;

/**
 * Add scope behaviour to an entity
 * @author Mathhieu Blottière <matthieu.blottiere@smile.fr>
 */
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