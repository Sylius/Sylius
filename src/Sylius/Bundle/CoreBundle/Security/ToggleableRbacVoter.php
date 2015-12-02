<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Security;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;
use Sylius\Component\Rbac\Security\RbacVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Toggleable RBAC voter.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ToggleableRbacVoter extends RbacVoter
{
    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param PermissionMapInterface   $permissionMap
     * @param RolesResolverInterface   $rolesResolver
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(PermissionMapInterface $permissionMap, RolesResolverInterface $rolesResolver, SettingsManagerInterface $settingsManager)
    {
        parent::__construct($permissionMap, $rolesResolver);

        $this->settingsManager = $settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (null === $this->settings) {
            $this->settings = $this->settingsManager->loadSettings('security');
        }

        if (false === $this->settings->get('enabled')) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        return parent::vote($token, $object, $attributes);
    }
}
