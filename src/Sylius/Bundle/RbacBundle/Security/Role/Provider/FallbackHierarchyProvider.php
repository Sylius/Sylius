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
    protected $default;

    /**
     * @var HierarchyProviderInterface
     */
    protected $fallback;

    /**
     * Constructor.
     *
     * @param HierarchyProviderInterface $default
     * @param HierarchyProviderInterface $fallback
     */
    public function __construct(HierarchyProviderInterface $default, HierarchyProviderInterface $fallback)
    {
        $this->default = $default;
        $this->fallback = $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function getMap()
    {
        try {
            return $this->default->getMap();
        } catch (\Exception $e) {
            return $this->fallback->getMap();
        }
    }
}
