<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Rbac\Context\RbacContextInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacContext implements RbacContextInterface
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
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (null === $this->settings) {
            $this->settings = $this->settingsManager->loadSettings('sylius_security');
        }

        return true === $this->settings->get('enabled');
    }
}
