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

trait FilesAwareTrait
{
    /**
     * @var Collection|FileInterface[]
     */
    protected $files;
    
    /**
     * {@inheritdoc}
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilesByType(string $type): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type): bool {
            return $type === $file->getType();
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilesByMimeType(string $type): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type): bool {
            return $type === $file->getMimeType();
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilesByTypeAndMimeType(string $type, string $mimeType): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type): bool {
            return ($type === $file->getType() && strpos($file->getMimeType(), 'image'));
        });
    }
        
    /**
     * {@inheritdoc}
     */
    public function hasFiles(): bool
    {
        return !$this->files->isEmpty();
    }
            
    /**
     * {@inheritdoc}
     */
    public function hasFile(FileInterface $file): bool
    {
        return $this->files->contains($file);
    }
    
    /**
     * {@inheritdoc}
     */
    public function addFile(FileInterface $file): void
    {
        $file->setOwner($this);
        $this->files->add($file);
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeFile(FileInterface $file): void
    {
        if ($this->hasFile($file)) {
            $file->setOwner(null);
            $this->files->removeElement($file);
        }
    }
        
    /**
     * {@inheritdoc}
     */
    public function getImages(): Collection
    {
        return $this->files->filter(function (FileInterface $file) : bool {
            return strpos($file->getMimeType(), 'image') !== false;
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasImages(): bool
    {
        return !$this->files->filter(function (FileInterface $file) : bool {
            return strpos($file->getMimeType(), 'image') !== false;
        })
        ->isEmpty();
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function getImagesByType(string $type): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type): bool {
            return ($type === $file->getType() && strpos($file->getMimeType(), 'image') !== false);
        });
    }
    
    
}
