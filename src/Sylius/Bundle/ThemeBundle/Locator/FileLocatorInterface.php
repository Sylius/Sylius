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

namespace Sylius\Bundle\ThemeBundle\Locator;

interface FileLocatorInterface
{
    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not valid or file was not found
     */
    public function locateFileNamed(string $name): string;

    /**
     * @param string $name
     *
     * @return array
     *
     * @throws \InvalidArgumentException If name is not valid or files were not found
     */
    public function locateFilesNamed(string $name): array;
}
