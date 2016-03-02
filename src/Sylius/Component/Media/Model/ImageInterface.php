<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Media\Model;

use Symfony\Cmf\Bundle\MediaBundle\ImageInterface as CmfImageInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
interface ImageInterface
{
    /**
     * @return string
     */
    public function getMediaId();

    /**
     * @param string $mediaId
     */
    public function setMediaId($mediaId);

    /**
     * @return CmfImageInterface
     */
    public function getMedia();

    /**
     * @param CmfImageInterface $media
     */
    public function setMedia($media);
}
