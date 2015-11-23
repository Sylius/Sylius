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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacVoterBridge implements VoterInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $roleRepository;

    /**
     * @var RepositoryInterface
     */
    protected $permissionRepository;

    /**
     * @var RbacVoterInterface
     */
    protected $rbacVoter;

    /**
     * @var array
     */
    private $cache;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $roleRepository
     * @param RepositoryInterface $permissionRepository
     */
    public function __construct(
        RepositoryInterface $roleRepository,
        RepositoryInterface $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
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
        if ($this->attributeExists($attribute, 'role')) {
            return true;
        }
        if ($this->attributeExists($attribute, 'permission')) {
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
            return self::ACCESS_ABSTAIN;
        }

        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $permissionCode) {
            if (!$this->supportsAttribute($permissionCode)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = self::ACCESS_DENIED;

            // grant access as soon as at least one voter returns a positive response
            if ($this->rbacVoter->isGranted($identity, $permissionCode, $object)) {
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    private function attributeExists($attribute, $type)
    {
        if (!isset($this->cache[$type])) {
            $this->cache[$type] = array();
        }

        if (!array_key_exists($attribute, $this->cache[$type])) {
            $this->cache[$type][$attribute] = $this->{ $type . 'Repository' }->findBy(array('code' => $attribute));
        }

        return null !== $this->cache[$type][$attribute];
    }
}
