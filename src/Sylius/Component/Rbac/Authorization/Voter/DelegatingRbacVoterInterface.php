<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Authorization\Voter;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
interface DelegatingRbacVoterInterface
{
    /**
     * Set resource resource voters.
     *
     * @param ResourceVoterInterface $resourceVoter
     * @param int                    $priority
     */
    public function addResourceVoter(ResourceVoterInterface $resourceVoter, $priority = null);
}
