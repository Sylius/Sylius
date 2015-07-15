<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Templating\Helper;

use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacHelper extends Helper
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Check if currently logged in identity is granted permission.
     *
     * @param string $permissionCode
     *
     * @return string
     */
    public function isGranted($permissionCode)
    {
        return $this->authorizationChecker->isGranted($permissionCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_rbac';
    }
}
