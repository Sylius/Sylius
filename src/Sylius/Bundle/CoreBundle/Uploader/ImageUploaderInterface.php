<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Uploader;

use Sylius\Bundle\CoreBundle\Model\ImageInterface;

interface ImageUploaderInterface
{
    /**
     * Uploads file for given model with new, unique filename & path.
     *
     * @param ImageInterface $image
     */
    public function upload(ImageInterface $image);

    /**
     * Removes file or directory.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function remove($path);
}
