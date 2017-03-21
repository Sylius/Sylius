<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ImageInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return null|\SplFileInfo
     */
    public function getFile();

    /**
     * @param \SplFileInfo $file
     */
    public function setFile(\SplFileInfo $file);

    /**
     * @return bool
     */
    public function hasFile();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return ImageOwnerInterface
     */
    public function getOwner();

    /**
     * @param ImageOwnerInterface|null $owner
     */
    public function setOwner(ImageOwnerInterface $owner = null);
}
