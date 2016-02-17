<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Locator;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface FileLocatorInterface
{
    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not valid or file was not found
     */
    public function locateFileNamed($name);

    /**
     * @param string $name
     *
     * @return array
     *
     * @throws \InvalidArgumentException If name is not valid or files were not found
     */
    public function locateFilesNamed($name);
}
