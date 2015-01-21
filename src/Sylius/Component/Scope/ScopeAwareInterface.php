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
 * Describe an entity that can live in a particular scope
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
interface ScopeAwareInterface
{
    /**
     * Get all scoped values
     *
     * @return ArrayCollection
     */
    public function getScopedValues();

    /**
     * Add a new scoped value
     *
     * @param ScopedValueInterface $scoped
     * @return self
     */
    public function addScopedValue(ScopedValueInterface $scoped);

    /**
     * Remove a scoped value
     *
     * @param ScopedValueInterface $scoped
     * @return self
     */
    public function removeScopedValue(ScopedValueInterface $scoped);

    /**
     * Set current scope
     *
     * @param ScopeInterface $currentScope
     * @return ScopeAwareInterface
     */
    public function setCurrentScope(ScopeInterface $currentScope = null);

    /**
     * Get current scope
     *
     * @return ScopeInterface|null
     */
    public function getCurrentScope();
}