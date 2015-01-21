<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Scope;

/**
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
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