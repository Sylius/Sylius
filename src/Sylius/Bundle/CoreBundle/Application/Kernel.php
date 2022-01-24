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

namespace Sylius\Bundle\CoreBundle\Application;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    use MicroKernelTrait;

    public const VERSION = '1.12.0-DEV';

    public const VERSION_ID = '11200';

    public const MAJOR_VERSION = '1';

    public const MINOR_VERSION = '12';

    public const RELEASE_VERSION = '0';

    public const EXTRA_VERSION = 'DEV';

    public function __construct(string $environment, bool $debug)
    {
        @trigger_error(sprintf('Using "%s" as Symfony kernel is deprecated since Sylius 1.3. Please migrate to Symfony 4 directory structure. Upgrade guide: https://github.com/Sylius/Sylius/blob/1.3/UPGRADE-1.3.md#directory-structure-change', self::class), \E_USER_DEPRECATED);

        parent::__construct($environment, $debug);
    }
}
