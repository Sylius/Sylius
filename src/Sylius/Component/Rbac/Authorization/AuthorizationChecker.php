<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Authorization;

use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Sylius\Component\Rbac\Authorization\Voter\RbacVoterInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;

/**
 * Default authorization checker.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Christian Daguerre <christian@daguer.re>
 */
class AuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var CurrentIdentityProviderInterface
     */
    protected $currentIdentityProvider;

    /**
     * @var RbacVoterInterface
     */
    protected $voter;

    /**
     * @param CurrentIdentityProviderInterface $currentIdentityProvider
     * @param RbacVoterInterface                   $voter
     */
    public function __construct(CurrentIdentityProviderInterface $currentIdentityProvider, RbacVoterInterface $voter)
    {
        $this->currentIdentityProvider = $currentIdentityProvider;
        $this->voter = $voter;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($permissionCode, $resource = null)
    {
        $identity = $this->currentIdentityProvider->getIdentity();

        if (null === $identity) {
            return false;
        }

        if (!$identity instanceof IdentityInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Current identity must implement "%s".',
                IdentityInterface::class
            ));
        }

        return $this->voter->isGranted($identity, $permissionCode, $resource);
    }
}
