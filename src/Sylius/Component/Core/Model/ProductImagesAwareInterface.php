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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Ahmed Kooli <kooliahmd@gmail.com>
 */
interface ProductImagesAwareInterface
{
    /**
     * @return Collection|ImageInterface[]
     */
    public function getImages();

    /**
     * @param string $type
     *
     * @return Collection|ImageInterface[]
     */
    public function getImagesByType($type);

    /**
     * @return bool
     */
    public function hasImages();

    /**
     * @param ImageInterface $image
     *
     * @return bool
     */
    public function hasImage(ProductImageInterface $image);

    /**
     * @param ImageInterface $image
     */
    public function addImage(ProductImageInterface $image);

    /**
     * @param ImageInterface $image
     */
    public function removeImage(ProductImageInterface $image);
}
