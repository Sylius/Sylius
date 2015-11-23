<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class ToggleableVoter implements VoterInterface
{
    /**
     * @var VoterInterface
     */
    protected $voter;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param VoterInterface           $voter
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(VoterInterface $voter, SettingsManagerInterface $settingsManager)
    {
        $this->voter = $voter;
        $this->settingsManager = $settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return $this->voter->supportsAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return $this->voter->supportsClass($class);
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->isEnabled()) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        return $this->voter->vote($token, $object, $attributes);
    }

    /**
     * @return bool
     */
    protected function isEnabled()
    {
        if (null === $this->settings) {
            $this->settings = $this->settingsManager->loadSettings('sylius_security');
        }

        return true === $this->settings->get('enabled');
    }
}
