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
 * Describe a value living in a particular scope
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
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