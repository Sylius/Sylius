<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Twig;

use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;

/**
 * Sylius RBAC Twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class RbacExtension extends \Twig_Extension
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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_is_granted', array($this, 'isGranted')),
        );
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
