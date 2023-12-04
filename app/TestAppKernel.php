<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @internal
 */

declare(strict_types=1);

trigger_deprecation(
    'sylius/sylius',
    '1.3',
    'The "TestAppKernel" class located at "app/TestAppKernel.php" is deprecated. Use "Kernel" class located at "src/Kernel.php" instead.',
);

class_alias(Kernel::class, TestAppKernel::class);
