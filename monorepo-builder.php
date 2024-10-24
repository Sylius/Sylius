<?php

declare(strict_types=1);

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $config): void {
    $config->packageDirectories([
        // default value
        __DIR__ . '/src',
    ]);
};
