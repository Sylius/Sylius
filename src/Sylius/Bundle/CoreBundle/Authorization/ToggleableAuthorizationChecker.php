<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Authorization;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ToggleableAuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, SettingsManagerInterface $settingsManager)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->settingsManager = $settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($permissionCode)
    {
        if (null === $this->settings) {
            $this->settings = $this->settingsManager->load('sylius_security');
        }

        if (false === $this->settings->get('enabled')) {
            return true;
        }

        return $this->authorizationChecker->isGranted($permissionCode);
    }
}
