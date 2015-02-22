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

/**
 * Test (toggleable) authorization checker.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TestAuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($permissionCode)
    {
        if (!$this->enabled) {
            return true;
        }

        return $this->authorizationChecker->isGranted($permissionCode);
    }

    /**
     * @param $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (Boolean) $enabled;
    }
}
