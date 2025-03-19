<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

use Symfony\Contracts\Translation\TranslatorInterface;

final class FilesystemRequirements extends RequirementCollection
{
    public function __construct(TranslatorInterface $translator, string $cacheDir, string $logsDir)
    {
        parent::__construct($translator->trans('sylius.installer.filesystem.header', []));

        $this
            ->add(new Requirement(
                $translator->trans('sylius.installer.filesystem.cache.header', []),
                is_writable($cacheDir),
                true,
                $translator->trans('sylius.installer.filesystem.cache.help', ['%path%' => $cacheDir]),
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.filesystem.logs.header', []),
                is_writable($logsDir),
                true,
                $translator->trans('sylius.installer.filesystem.logs.help', ['%path%' => $logsDir]),
            ))
        ;
    }
}
