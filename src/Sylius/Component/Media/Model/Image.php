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

use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface as CmfImageInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class Image implements ImageInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $mediaId;

    /**
     * @var CmfImageInterface
     */
    protected $media;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * {@inheritdoc}
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
    }

    /**
     * {@inheritdoc}
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * {@inheritdoc}
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }
}
