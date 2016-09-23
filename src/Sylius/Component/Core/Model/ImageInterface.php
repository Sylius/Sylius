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
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ImageInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return bool
     */
    public function hasFile();

    /**
     * @return null|\SplFileInfo
     */
    public function getFile();

    /**
     * @param \SplFileInfo $file
     */
    public function setFile(\SplFileInfo $file);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return ImageAwareInterface
     */
    public function getOwner();

    /**
     * @param ImageAwareInterface $owner
     */
    public function setOwner(ImageAwareInterface $owner = null);
}
