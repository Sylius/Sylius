<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

use Symfony\Component\Translation\TranslatorInterface;

class SettingsRequirements extends RequirementCollection
{
    const RECOMMENDED_PHP_VERSION = '7.0';

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator->trans('sylius.settings.header', [], 'requirements'));

        $this
            ->add(new Requirement(
                $translator->trans('sylius.settings.timezone', [], 'requirements'),
                $this->isOn('date.timezone'),
                true
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.version_recommended', [], 'requirements'),
                version_compare(phpversion(), self::RECOMMENDED_PHP_VERSION, '>='),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.detect_unicode', [], 'requirements'),
                !$this->isOn('detect_unicode'),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.session.auto_start', [], 'requirements'),
                !$this->isOn('session.auto_start'),
                false
            ))
        ;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    private function isOn($key)
    {
        $value = ini_get($key);

        return !empty($value) && $value !== 'off';
    }
}
