<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sylius\Component\Rbac\Authorization\Voter\RbacVoterInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Provider\CredentialProviderInterface;

/**
 * Bridges Sylius RBAC voters to Symfony voters.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacVoterBridge implements VoterInterface
{
    /**
     * @var CredentialProviderInterface
     */
    protected $credentialProvider;

    /**
     * @var RbacVoterInterface
     */
    protected $rbacVoter;

    /**
     * Constructor.
     *
     * @param CredentialProviderInterface $credentialProvider
     */
    public function __construct(CredentialProviderInterface $credentialProvider)
    {
        $this->credentialProvider = $credentialProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setRbacVoter(RbacVoterInterface $rbacVoter)
    {
        $this->rbacVoter = $rbacVoter;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        if ($this->credentialProvider->hasRole($attribute)) {
            return true;
        }
        if ($this->credentialProvider->hasPermission($attribute)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->rbacVoter) {
            throw new \Exception('The underlying RBAC voter was not set.');
        }

        $identity = $token->getUser();

        if (!$identity instanceof IdentityInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // abstain vote by default in case none of the attributes are supported
        $vote = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $permissionCode) {
            if (!$this->supportsAttribute($permissionCode)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = VoterInterface::ACCESS_DENIED;

            // grant access as soon as at least one voter returns a positive response
            if ($this->rbacVoter->isGranted($identity, $permissionCode, $object)) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return $vote;
    }
}
