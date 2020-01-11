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

interface ProductImagesAwareInterface
{
    /**
     * @return Collection|ImageInterface[]
     *
     * @psalm-return Collection<array-key, ImageInterface>
     */
    public function getImages(): Collection;

    /**
     * @return Collection|ImageInterface[]
     *
     * @psalm-return Collection<array-key, ImageInterface>
     */
    public function getImagesByType(string $type): Collection;

    public function hasImages(): bool;

    public function hasImage(ProductImageInterface $image): bool;

    public function addImage(ProductImageInterface $image): void;

    public function removeImage(ProductImageInterface $image): void;
}
