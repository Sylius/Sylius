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

use Sylius\Component\Rbac\Model\IdentityInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class DelegatingRbacVoter extends DefaultRbacVoter implements DelegatingRbacVoterInterface
{
    /**
     * @var array|ResourceVoterInterface[]
     */
    protected $resourceVoters;

    /**
     * @var bool
     */
    private $sorted = false;

    /**
     * {@inheritdoc}
     */
    public function addResourceVoter(ResourceVoterInterface $resourceVoter)
    {
        $this->resourceVoters[] = $resourceVoter;
    }

    /**
     * {@inheritdoc}
     */
    protected function hasPermissionForResource(IdentityInterface $identity, $permissionCode, $resource)
    {
        if (!$this->sorted) {
            $this->sortVoters();
        }

        foreach ($this->resourceVoters as $voter) {
            if ($voter->supports($permissionCode, $resource)) {
                return $voter->isGranted($identity, $permissionCode, $resource);
            }
        }

        return true;
    }

    private function sortVoters()
    {
        usort($this->resourceVoters, function (ResourceVoterInterface $voter1, ResourceVoterInterface $voter2) {
            if ($voter1->getPriority() === $voter2->getPriority()) {
                return 0;
            }
            return ($voter1->getPriority() < $voter2->getPriority()) ? -1 : 1;
        });
    }
}
