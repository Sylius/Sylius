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

namespace Sylius\Bundle\CoreBundle\Application;

use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;

/**
 * @deprecated Using "Sylius\Bundle\CoreBundle\Application\Kernel" as Symfony kernel is deprecated since Sylius 1.3.
 *             Please migrate to Symfony 4 directory structure.
 *             Upgrade guide: https://github.com/Sylius/Sylius/blob/1.3/UPGRADE-1.3.md#directory-structure-change
 *
 * @final
 */
class Kernel extends HttpKernel
{
    use MicroKernelTrait;

    /** @deprecated Use Sylius\Bundle\CoreBundle\SyliusCoreBundle::VERSION instead */
    public const VERSION = SyliusCoreBundle::VERSION;

    /** @deprecated Use Sylius\Bundle\CoreBundle\SyliusCoreBundle::VERSION_ID instead */
    public const VERSION_ID = SyliusCoreBundle::VERSION_ID;

    /** @deprecated Use Sylius\Bundle\CoreBundle\SyliusCoreBundle:MAJOR_VERSION instead */
    public const MAJOR_VERSION = SyliusCoreBundle::MAJOR_VERSION;

    /** @deprecated Use Sylius\Bundle\CoreBundle\SyliusCoreBundle:MINOR_VERSION instead */
    public const MINOR_VERSION = SyliusCoreBundle::MINOR_VERSION;

    /** @deprecated Use Sylius\Bundle\CoreBundle\SyliusCoreBundle:RELEASE_VERSION instead */
    public const RELEASE_VERSION = SyliusCoreBundle::RELEASE_VERSION;

    /** @deprecated Use Sylius\Bundle\CoreBundle\SyliusCoreBundle:EXTRA_VERSION instead */
    public const EXTRA_VERSION = SyliusCoreBundle::EXTRA_VERSION;

    public function __construct(string $environment, bool $debug)
    {
        trigger_deprecation(
            'sylius/core-bundle',
            '1.3',
            'Using "%s" as Symfony kernel is deprecated. Please migrate to Symfony 4 directory structure. Upgrade guide: https://github.com/Sylius/Sylius/blob/1.3/UPGRADE-1.3.md#directory-structure-change',
            self::class,
        );

        parent::__construct($environment, $debug);
    }
}
