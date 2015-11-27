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
interface HierarchyProviderInterface
{
    /**
     * @return array
     */
    public function getMap();
}
