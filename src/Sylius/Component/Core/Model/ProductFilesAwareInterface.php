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

interface ProductFilesAwareInterface
{    
    /**
     * @return Collection|ProductFileInterface[]
     */
    public function getFiles(): Collection;
    /**
     *
     * @param string $type
     *
     * @return Collection|ProductFileInterface[]
     */
    public function getFilesByTypeAndMimeType(string $type, string $mimeType): Collection;
    
    /**
     * @param string $type
     *
     * @return Collection|ProductFileInterface[]
     */
    public function getFilesByType(string $type): Collection;
    
    /**
     * @return bool
     */
    public function hasFiles(): bool;
    
    /**
     * @param ProductFileInterface $file
     *
     * @return bool
     */
    public function hasFile(ProductFileInterface $file): bool;
    
    /**
     * @return Collection|ProductFileInterface[]
     */
    public function getImages(): Collection;
    
    /**
     * @param string $type
     *
     * @return Collection|ProductFileInterface[]
     */
    public function getImagesByType(string $type): Collection;
    
    /**
     * @return bool
     */
    public function hasImages(): bool;
    
    /**
     * @param ProductFileInterface $file
     */
    public function addFile(ProductFileInterface $file): void;
    
    /**
     * @param ProductFileInterface $file
     */
    public function removeFile(ProductFileInterface $file): void;
}
