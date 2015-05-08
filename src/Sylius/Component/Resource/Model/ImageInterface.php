<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

use Symfony\Component\HttpFoundation\File\File;

interface ImageInterface extends TimestampableInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function hasFile();

    /**
     * @return null|File
     */
    public function getFile();

    /**
     * @param File $file
     *
     * @return self
     */
    public function setFile(File $file);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     *
     * @return self
     */
    public function setPath($path);
}
