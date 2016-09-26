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

class FilesystemRequirements extends RequirementCollection
{
    public function __construct(TranslatorInterface $translator, $root, $cacheDir, $logDir)
    {
        parent::__construct($translator->trans('sylius.filesystem.header', [], 'requirements'));

        $this
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.vendors', [], 'requirements'),
                is_dir($root.'/../vendor')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.cache.header', [], 'requirements'),
                is_writable($cacheDir),
                true,
                $translator->trans('sylius.filesystem.cache.help', ['%path%' => $cacheDir], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.logs.header', [], 'requirements'),
                is_writable($logDir),
                true,
                $translator->trans('sylius.filesystem.logs.help', ['%path%' => $logDir], 'requirements')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.filesystem.parameters.header', [], 'requirements'),
                is_writable($root.'/config/parameters.yml'),
                true,
                $translator->trans('sylius.filesystem.parameters.help', ['%path%' => $root.'/config/parameters.yml'], 'requirements')
            ))
        ;
    }
}
