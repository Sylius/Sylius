<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Context;

use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
interface RbacContextInterface
{
    /**
     * Whether RBAC is enabled AND initialized.
     *
     * @return bool
     */
    public function isEnabled();
}
