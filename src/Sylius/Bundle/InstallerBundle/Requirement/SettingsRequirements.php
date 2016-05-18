<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Requirement;

use DateTimeZone;
use Symfony\Component\Translation\TranslatorInterface;

class SettingsRequirements extends RequirementCollection
{
    const REQUIRED_PHP_VERSION = '5.5.9';
    const RECOMMENDED_PHP_VERSION = '5.6.13';

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator->trans('sylius.settings', [], 'requirements'));

        $on = $translator->trans('sylius.settings.on', [], 'requirements');
        $off = $translator->trans('sylius.settings.off', [], 'requirements');

        $this
            ->add(new Requirement(
                $translator->trans('sylius.settings.version', [], 'requirements'),
                version_compare(phpversion(), self::REQUIRED_PHP_VERSION, '>='),
                '>='.self::REQUIRED_PHP_VERSION,
                phpversion()
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.version_recommended', [], 'requirements'),
                version_compare(phpversion(), self::RECOMMENDED_PHP_VERSION, '>='),
                '>='.self::RECOMMENDED_PHP_VERSION,
                phpversion(),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.timezone', [], 'requirements'),
                $this->isOn('date.timezone'),
                $translator->trans('sylius.settings.any', [], 'requirements'),
                ini_get('date.timezone')
            ))
        ;

        if (version_compare(phpversion(), self::REQUIRED_PHP_VERSION, '>=')) {
            $this->add(new Requirement(
                $translator->trans('sylius.settings.timezone_deprecated', [], 'requirements'),
                in_array(date_default_timezone_get(), DateTimeZone::listIdentifiers()),
                $translator->trans('sylius.settings.non_deprecated', [], 'requirements'),
                date_default_timezone_get(),
                true,
                $translator->trans('sylius.settings.timezone_deprecated.help', ['%timezone%' => date_default_timezone_get()], 'requirements')
            ));
        }

        $this
            ->add(new Requirement(
                $translator->trans('sylius.settings.detect_unicode', [], 'requirements'),
                !$this->isOn('detect_unicode'),
                $on,
                ini_get('detect_unicode'),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.detect_unicode', [], 'requirements'),
                !$this->isOn('detect_unicode'),
                $on,
                ini_get('detect_unicode'),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.short_open_tag', [], 'requirements'),
                !$this->isOn('short_open_tag'),
                $off,
                ini_get('short_open_tag'),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.magic_quotes_gpc', [], 'requirements'),
                !$this->isOn('magic_quotes_gpc'),
                $off,
                ini_get('magic_quotes_gpc'),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.register_globals', [], 'requirements'),
                !$this->isOn('register_globals'),
                $off,
                ini_get('register_globals'),
                false
            ))
            ->add(new Requirement(
                $translator->trans('sylius.settings.session.auto_start', [], 'requirements'),
                !$this->isOn('session.auto_start'),
                $off,
                ini_get('session.auto_start'),
                false
            ))
        ;
    }

    private function isOn($key)
    {
        $value = ini_get($key);

        return false !== $value && 'off' !== $value;
    }
}
