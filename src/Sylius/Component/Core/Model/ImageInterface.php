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

use Sylius\Component\Resource\Model\ResourceInterface;

interface ImageInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getType(): ?string;

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void;

    /**
     * @return \SplFileInfo|null
     */
    public function getFile(): ?\SplFileInfo;

    /**
     * @param \SplFileInfo|null $file
     */
    public function setFile(?\SplFileInfo $file): void;

    /**
     * @return bool
     */
    public function hasFile(): bool;

    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): void;

    /**
     * @return object
     */
    public function getOwner();

    /**
     * @param object|null $owner
     */
    public function setOwner($owner): void;
}
