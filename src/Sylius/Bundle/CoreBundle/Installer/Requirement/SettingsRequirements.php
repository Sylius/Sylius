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

final class SettingsRequirements extends RequirementCollection
{
    const RECOMMENDED_PHP_VERSION = '7.0';

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator->trans('sylius.installer.settings.header', []));

        $this
            ->add(new Requirement(
                $translator->trans('sylius.installer.settings.timezone', []),
                $this->isOn('date.timezone'),
                true
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.settings.version_recommended', []),
                version_compare(phpversion(), self::RECOMMENDED_PHP_VERSION, '>='),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.settings.detect_unicode', []),
                !$this->isOn('detect_unicode'),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.settings.session.auto_start', []),
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
