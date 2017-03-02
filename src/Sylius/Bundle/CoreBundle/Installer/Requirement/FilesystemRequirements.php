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

final class FilesystemRequirements extends RequirementCollection
{
    public function __construct(TranslatorInterface $translator, $root, $cacheDir, $logDir)
    {
        parent::__construct($translator->trans('sylius.installer.filesystem.header', []));

        $this
            ->add(new Requirement(
                $translator->trans('sylius.installer.filesystem.vendors', []),
                is_dir($root.'/../vendor')
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.filesystem.cache.header', []),
                is_writable($cacheDir),
                true,
                $translator->trans('sylius.installer.filesystem.cache.help', ['%path%' => $cacheDir])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.filesystem.logs.header', []),
                is_writable($logDir),
                true,
                $translator->trans('sylius.installer.filesystem.logs.help', ['%path%' => $logDir])
            ))
        ;
    }
}
