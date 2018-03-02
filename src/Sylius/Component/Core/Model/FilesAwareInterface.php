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

interface FilesAwareInterface
{
    /**
     * @return Collection|FileInterface[]
     */
    public function getImages(): Collection;

    /**
     * @return Collection|FileInterface[]
     */
    public function getFiles(): Collection;
    /**
     * 
     * @param string $type
     *
     * @return Collection|FileInterface[]
     */
    public function getFilesByTypeAndMimeType(string $type, string $mimeType): Collection;
    
    /**
     * @param string $type
     *
     * @return Collection|FileInterface[]
     */
    public function getFilesByType(string $type): Collection;
    
    /**
     * @return bool
     */
    public function hasFiles(): bool;
    
    /**
     * @param FileInterface $file
     *
     * @return bool
     */
    public function hasFile(FileInterface $file): bool;
    
    /**
     * @param string $type
     *
     * @return Collection|FileInterface[]
     */
    public function getImagesByType(string $type): Collection;
    
    /**
     * @return bool
     */
    public function hasImages(): bool;
    
    /**
     * @param FileInterface $file
     */
    public function addFile(FileInterface $file): void;

    /**
     * @param FileInterface $file
     */
    public function removeFile(FileInterface $file): void;
}
