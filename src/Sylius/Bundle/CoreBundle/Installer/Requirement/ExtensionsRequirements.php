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

use ReflectionExtension;
use Symfony\Component\Translation\TranslatorInterface;

class ExtensionsRequirements extends RequirementCollection
{
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator->trans('sylius.extensions.header', [], 'requirements'));

        $this
            ->add(new Requirement(
                $translator->trans('sylius.extensions.json_encode', [], 'requirements'),
                function_exists('json_encode'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'JSON'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.session_start', [], 'requirements'),
                function_exists('session_start'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'session'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.ctype', [], 'requirements'),
                function_exists('ctype_alpha'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'ctype'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.token_get_all', [], 'requirements'),
                function_exists('token_get_all'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'JSON'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.simplexml_import_dom', [], 'requirements'),
                function_exists('simplexml_import_dom'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'SimpleXML'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.apc', [], 'requirements'),
                !(function_exists('apc_store') && ini_get('apc.enabled')) || version_compare(phpversion('apc'), '3.0.17', '>='),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'APC (>=3.0.17)'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.pcre', [], 'requirements'),
                defined('PCRE_VERSION') ? ((float) PCRE_VERSION) > 8.0 : false,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'PCRE (>=8.0)'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.php_xml', [], 'requirements'),
                class_exists(\DomDocument::class),
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'PHP-XML'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.mbstring', [], 'requirements'),
                function_exists('mb_strlen'),
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'mbstring'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.iconv', [], 'requirements'),
                function_exists('iconv'),
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'iconv'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.exif', [], 'requirements'),
                function_exists('exif_read_data'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'exif'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.intl', [], 'requirements'),
                extension_loaded('intl'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'intl'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.fileinfo', [], 'requirements'),
                extension_loaded('fileinfo'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'fileinfo'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.accelerator.header', [], 'requirements'),
                !empty(ini_get('opcache.enable')),
                false,
                $translator->trans('sylius.extensions.accelerator.help', [], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.pdo', [], 'requirements'),
                class_exists('PDO'),
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'PDO'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.gd', [], 'requirements'),
                defined('GD_VERSION'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'gd'], 'requirements')
            ))
        ;

        if (extension_loaded('intl')) {
            if (defined('INTL_ICU_VERSION')) {
                $version = INTL_ICU_VERSION;
            } else {
                $reflector = new ReflectionExtension('intl');

                ob_start();
                $reflector->info();
                $output = strip_tags(ob_get_clean());

                preg_match('/^ICU version +(?:=> )?(.*)$/m', $output, $matches);
                $version = $matches[1];
            }

            $this->add(new Requirement(
                $translator->trans('sylius.extensions.icu', [], 'requirements'),
                version_compare($version, '4.0', '>='),
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'ICU (>=4.0)'], 'requirements')
            ));
        }
    }
}
