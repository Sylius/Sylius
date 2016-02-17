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

use ReflectionExtension;
use Symfony\Component\Translation\TranslatorInterface;

class ExtensionsRequirements extends RequirementCollection
{
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator->trans('sylius.extensions', [], 'requirements'));

        $on = $translator->trans('sylius.extensions.on', [], 'requirements');
        $off = $translator->trans('sylius.extensions.off', [], 'requirements');

        $pcreVersion = defined('PCRE_VERSION') ? (float) PCRE_VERSION : null;

        $this
            ->add(new Requirement(
                $translator->trans('sylius.extensions.json_encode', [], 'requirements'),
                $status = function_exists('json_encode'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'JSON'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.session_start', [], 'requirements'),
                $status = function_exists('session_start'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'session'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.ctype', [], 'requirements'),
                $status = function_exists('ctype_alpha'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'ctype'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.token_get_all', [], 'requirements'),
                $status = function_exists('token_get_all'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'JSON'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.simplexml_import_dom', [], 'requirements'),
                $status = function_exists('simplexml_import_dom'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'SimpleXML'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.apc', [], 'requirements'),
                !(function_exists('apc_store') && ini_get('apc.enabled')) || version_compare(phpversion('apc'), '3.0.17', '>='),
                '>=3.0.17',
                phpversion('apc'),
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'APC (>=3.0.17)'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.pcre', [], 'requirements'),
                null !== $pcreVersion && $pcreVersion > 8.0,
                '>=8.0',
                $pcreVersion,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'PCRE (>=8.0)'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.php_xml', [], 'requirements'),
                $status = class_exists(\DomDocument::class),
                $on,
                $status ? $on : $off,
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'PHP-XML'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.mbstring', [], 'requirements'),
                $status = function_exists('mb_strlen'),
                $on,
                $status ? $on : $off,
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'mbstring'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.iconv', [], 'requirements'),
                $status = function_exists('iconv'),
                $on,
                $status ? $on : $off,
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'iconv'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.exif', [], 'requirements'),
                $status = function_exists('exif_read_data'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'exif'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.intl', [], 'requirements'),
                $status = extension_loaded('intl'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'intl'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.fileinfo', [], 'requirements'),
                $status = extension_loaded('fileinfo'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'fileinfo'], 'requirements')
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
                '4.0',
                $version,
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'ICU (>=4.0)'], 'requirements')
            ));
        }

        $status = (function_exists('apc_store') && ini_get('apc.enabled'))
            || function_exists('eaccelerator_put') && ini_get('eaccelerator.enable')
            || function_exists('xcache_set')
            || function_exists('zend_optimizer_version')
        ;

        $this
            ->add(new Requirement(
                $translator->trans('sylius.extensions.accelerator', [], 'requirements'),
                $status,
                $on,
                $status ? $on : $off,
                false,
                $translator->trans('sylius.extensions.accelerator.help', [], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.pdo', [], 'requirements'),
                $status = class_exists('PDO'),
                $on,
                $status ? $on : $off,
                false,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'PDO'], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.extensions.gd', [], 'requirements'),
                $status = defined('GD_VERSION'),
                $on,
                $status ? $on : $off,
                true,
                $translator->trans('sylius.extensions.help', ['%extension%' => 'gd'], 'requirements')
            ))
        ;
    }
}
