<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

use Symfony\Component\Translation\TranslatorInterface;

final class FilesystemRequirements extends RequirementCollection
{
    /**
     * @param TranslatorInterface $translator
     * @param string $cacheDir
     * @param string $logsDir
     * @param string $rootDir Deprecated.
     */
    public function __construct(TranslatorInterface $translator, string $cacheDir, string $logsDir, string $rootDir = null)
    {
        parent::__construct($translator->trans('sylius.installer.filesystem.header', []));

        if (func_num_args() >= 4) {
            @trigger_error(sprintf(
                'Passing root directory to "%s" constructor as the second argument is deprecated since 1.2 ' .
                'and this argument will be removed in 2.0.',
                self::class
            ), E_USER_DEPRECATED);

            [$rootDir, $cacheDir, $logsDir] = [$cacheDir, $logsDir, $rootDir];
        }

        $this
            ->add(new Requirement(
                $translator->trans('sylius.installer.filesystem.cache.header', []),
                is_writable($cacheDir),
                true,
                $translator->trans('sylius.installer.filesystem.cache.help', ['%path%' => $cacheDir])
            ))
            ->add(new Requirement(
                $translator->trans('sylius.installer.filesystem.logs.header', []),
                is_writable($logsDir),
                true,
                $translator->trans('sylius.installer.filesystem.logs.help', ['%path%' => $logsDir])
            ))
        ;
    }
}
