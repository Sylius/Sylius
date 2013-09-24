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
    public function __construct(TranslatorInterface $translator, $root)
    {
        parent::__construct($translator->trans('sylius.filesystem', array(), 'requirements'));

        $exists = $translator->trans('sylius.filesystem.exists', array(), 'requirements');
        $notExists = $translator->trans('sylius.filesystem.not_exists', array(), 'requirements');
        $writable = $translator->trans('sylius.filesystem.writable', array(), 'requirements');
        $notWritable = $translator->trans('sylius.filesystem.not_writable', array(), 'requirements');

        $this
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.vendors', array(), 'requirements'),
                $status = is_dir($root.'/../vendor'),
                $exists,
                $status ? $exists : $notExists
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.cache', array(), 'requirements'),
                $status = is_writable($root.'/cache'),
                $translator->trans('sylius.filesystem.writable', array(), 'requirements'),
                $status ? $translator->trans('sylius.filesystem.writable', array(), 'requirements') : $translator->trans('sylius.filesystem.not_writable', array(), 'requirements'),
                true,
                $translator->trans('sylius.filesystem.cache.help', array('%path%' => $root.'/cache'), 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.logs', array(), 'requirements'),
                $status = is_writable($root.'/logs'),
                $writable,
                $status ? $writable : $notWritable,
                true,
                $translator->trans('sylius.filesystem.logs.help', array('%path%' => $root.'/logs'), 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.parameters', array(), 'requirements'),
                $status = is_writable($root.'/config/parameters.yml'),
                $writable,
                $status ? $writable : $notWritable,
                true,
                $translator->trans('sylius.filesystem.parameters.help', array('%path%' => $root.'/config/parameters.yml'), 'requirements')
            ))
        ;
    }
}
