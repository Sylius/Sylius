<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @internal
 */

declare(strict_types=1);

@trigger_error('The "AppKernel" class located at "app/AppKernel.php" is deprecated since Sylius 1.3. Use "Kernel" class located at "src/Kernel.php" instead.', E_USER_DEPRECATED);

class_alias(Kernel::class, AppKernel::class);
