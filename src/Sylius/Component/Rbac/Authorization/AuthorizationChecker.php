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

use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Default authorization checker.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class AuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var CurrentIdentityProviderInterface
     */
    protected $currentIdentityProvider;

    /**
     * @var VoterInterface
     */
    protected $voter;

    /**
     * @param CurrentIdentityProviderInterface $currentIdentityProvider
     * @param VoterInterface                   $voter
     */
    public function __construct(CurrentIdentityProviderInterface $currentIdentityProvider, VoterInterface $voter)
    {
        $this->currentIdentityProvider = $currentIdentityProvider;
        $this->voter = $voter;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($permissionCode)
    {
        $identity = $this->currentIdentityProvider->getIdentity();
        if (null === $identity) {
            return false;
        }

        if (!$identity instanceof IdentityInterface) {
            throw new \InvalidArgumentException('Current identity must implement "Sylius\Component\Rbac\Model\IdentityInterface".');
        }

        return VoterInterface::ACCESS_GRANTED === $this->voter->vote(new AnonymousToken('sylius', $identity), $identity, array($permissionCode));
    }
}
