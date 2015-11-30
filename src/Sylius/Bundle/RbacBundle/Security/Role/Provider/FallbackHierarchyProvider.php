<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Security\Role\Provider;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class FallbackHierarchyProvider implements HierarchyProviderInterface
{
    /**
     * @var HierarchyProviderInterface
     */
    protected $defaultHierarchyProvider;

    /**
     * @var HierarchyProviderInterface
     */
    protected $fallbackHierarchyProvider;

    /**
     * @param HierarchyProviderInterface $defaultHierarchyProvider
     * @param HierarchyProviderInterface $fallbackHierarchyProvider
     */
    public function __construct(
        HierarchyProviderInterface $defaultHierarchyProvider,
        HierarchyProviderInterface $fallbackHierarchyProvider
    ) {
        $this->defaultHierarchyProvider = $defaultHierarchyProvider;
        $this->fallbackHierarchyProvider = $fallbackHierarchyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getMap()
    {
        try {
            return $this->defaultHierarchyProvider->getMap();
        } catch (\Exception $e) {
            return $this->fallbackHierarchyProvider->getMap();
        }
    }
}
