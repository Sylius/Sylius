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

use Doctrine\Common\Collections\Collection;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ImageAwareInterface
{
    /**
     * @return Collection|ImageInterface[]
     */
    public function getImages();

    /**
     * @param $code
     *
     * @return ImageInterface|null;
     */
    public function getImageByCode($code);

    /**
     * @return bool
     */
    public function hasImages();

    /**
     * @param ImageInterface $image
     *
     * @return bool
     */
    public function hasImage(ImageInterface $image);

    /**
     * @param ImageInterface $image
     */
    public function addImage(ImageInterface $image);

    /**
     * @param ImageInterface $image
     */
    public function removeImage(ImageInterface $image);
}
