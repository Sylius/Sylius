<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model\Twitter;

/**
 * {@inheritdoc}
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PlayerCard extends AbstractCard implements PlayerCardInterface
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'player';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $player;

    /**
     * @var integer
     */
    protected $playerWidth;

    /**
     * @var integer
     */
    protected $playerHeight;

    /**
     * @var string
     */
    protected $playerStream;

    /**
     * @var string
     */
    protected $playerStreamContentType;

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return json_encode([
            $this->site,
            $this->siteId,
            $this->title,
            $this->description,
            $this->image,
            $this->player,
            $this->playerWidth,
            $this->playerHeight,
            $this->playerStream,
            $this->playerStreamContentType,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->site,
            $this->siteId,
            $this->title,
            $this->description,
            $this->image,
            $this->player,
            $this->playerWidth,
            $this->playerHeight,
            $this->playerStream,
            $this->playerStreamContentType,
        ) = json_decode($serialized, true);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @inheritDoc
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlayerWidth($playerWidth)
    {
        $this->playerWidth = $playerWidth;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlayerWidth()
    {
        return $this->playerWidth;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlayerHeight($playerHeight)
    {
        $this->playerHeight = $playerHeight;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlayerHeight()
    {
        return $this->playerHeight;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlayerStream($playerStream)
    {
        $this->playerStream = $playerStream;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlayerStream()
    {
        return $this->playerStream;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlayerStreamContentType($playerStreamContentType)
    {
        $this->playerStreamContentType = $playerStreamContentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlayerStreamContentType()
    {
        return $this->playerStreamContentType;
    }
}
