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

use Symfony\Component\Translation\TranslatorInterface;

class FilesystemRequirements extends RequirementCollection
{
    public function __construct(TranslatorInterface $translator, $root, $cacheDir, $logDir)
    {
        parent::__construct($translator->trans('sylius.filesystem', [], 'requirements'));

        $exists = $translator->trans('sylius.filesystem.exists', [], 'requirements');
        $notExists = $translator->trans('sylius.filesystem.not_exists', [], 'requirements');
        $writable = $translator->trans('sylius.filesystem.writable', [], 'requirements');
        $notWritable = $translator->trans('sylius.filesystem.not_writable', [], 'requirements');

        $this
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.vendors', [], 'requirements'),
                $status = is_dir($root.'/../vendor'),
                $exists,
                $status ? $exists : $notExists
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.cache', [], 'requirements'),
                $status = is_writable($cacheDir),
                $translator->trans('sylius.filesystem.writable', [], 'requirements'),
                $status ? $translator->trans('sylius.filesystem.writable', [], 'requirements') : $translator->trans('sylius.filesystem.not_writable', [], 'requirements'),
                true,
                $translator->trans('sylius.filesystem.cache.help', ['%path%' => $cacheDir], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.logs', [], 'requirements'),
                $status = is_writable($logDir),
                $writable,
                $status ? $writable : $notWritable,
                true,
                $translator->trans('sylius.filesystem.logs.help', ['%path%' => $logDir], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.parameters', [], 'requirements'),
                $status = is_writable($root.'/config/parameters.yml'),
                $writable,
                $status ? $writable : $notWritable,
                true,
                $translator->trans('sylius.filesystem.parameters.help', ['%path%' => $root.'/config/parameters.yml'], 'requirements')
            ))
        ;
    }
}
