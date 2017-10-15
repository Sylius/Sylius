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
    public function getImages(): Collection;

    /**
     * @param string $type
     *
     * @return Collection|ImageInterface[]
     */
    public function getImagesByType(string $type): Collection;

    /**
     * @return bool
     */
    public function hasImages(): bool;

    /**
     * @param ProductImageInterface $image
     *
     * @return bool
     */
    public function hasImage(ProductImageInterface $image): bool;

    /**
     * @param ProductImageInterface $image
     */
    public function addImage(ProductImageInterface $image): void;

    /**
     * @param ProductImageInterface $image
     */
    public function removeImage(ProductImageInterface $image): void;
}
