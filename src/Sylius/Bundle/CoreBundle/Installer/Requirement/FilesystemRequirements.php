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
    /**
     * @param string $rootDir Deprecated.
     */
    public function __construct(TranslatorInterface $translator, string $cacheDir, string $logsDir, ?string $rootDir = null)
    {
        parent::__construct($translator->trans('sylius.installer.filesystem.header', []));

        if (func_num_args() >= 4) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.2',
                'Passing root directory to "%s" constructor as the second argument is deprecated and this argument will be removed in Sylius 2.0.',
                self::class,
            );

            [$rootDir, $cacheDir, $logsDir] = [$cacheDir, $logsDir, $rootDir];
        }

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
