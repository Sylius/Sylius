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

final class ExtensionsRequirements extends RequirementCollection
{
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator->trans('sylius.installer.extensions.header', []));

        $this
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.json_encode', []),
                function_exists('json_encode'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'JSON'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.session_start', []),
                function_exists('session_start'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'session'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.ctype', []),
                function_exists('ctype_alpha'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'ctype'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.token_get_all', []),
                function_exists('token_get_all'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'JSON'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.simplexml_import_dom', []),
                function_exists('simplexml_import_dom'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'SimpleXML'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.apc', []),
                !(function_exists('apc_store') && ini_get('apc.enabled')) || version_compare(phpversion('apc'), '3.0.17', '>='),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'APC (>=3.0.17)'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.pcre', []),
                defined('PCRE_VERSION') ? ((float) PCRE_VERSION) > 8.0 : false,
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'PCRE (>=8.0)'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.php_xml', []),
                class_exists(\DOMDocument::class),
                false,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'PHP-XML'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.mbstring', []),
                function_exists('mb_strlen'),
                false,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'mbstring'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.iconv', []),
                function_exists('iconv'),
                false,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'iconv'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.exif', []),
                function_exists('exif_read_data'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'exif'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.intl', []),
                extension_loaded('intl'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'intl'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.fileinfo', []),
                extension_loaded('fileinfo'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'fileinfo'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.accelerator.header', []),
                !empty(ini_get('opcache.enable')),
                false,
                $translator->trans('sylius.installer.extensions.accelerator.help', [])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.pdo', []),
                class_exists('PDO'),
                false,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'PDO'])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.extensions.gd', []),
                defined('GD_VERSION'),
                true,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'gd'])
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
                $translator->trans('sylius.installer.extensions.icu', []),
                version_compare($version, '4.0', '>='),
                false,
                $translator->trans('sylius.installer.extensions.help', ['%extension%' => 'ICU (>=4.0)'])
            ));
        }
    }
}
