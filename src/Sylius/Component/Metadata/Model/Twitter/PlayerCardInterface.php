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
 * @see https://dev.twitter.com/cards/types/player
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface PlayerCardInterface extends CardInterface
{
    /**
     * The twitter:title property.
     *
     * @return string
     */
    public function getTitle();

    /**
     * The twitter:title property.
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * The twitter:description property.
     *
     * @return string
     */
    public function getDescription();

    /**
     * The twitter:description property.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * The twitter:image property.
     *
     * @return string
     */
    public function getImage();

    /**
     * The twitter:image property.
     *
     * @param string $image
     */
    public function setImage($image);

    /**
     * The twitter:player property.
     *
     * @return string
     */
    public function getPlayer();

    /**
     * The twitter:player property.
     *
     * @param string $player
     */
    public function setPlayer($player);

    /**
     * The twitter:player:width property.
     *
     * @return int
     */
    public function getPlayerWidth();

    /**
     * The twitter:player:width property.
     *
     * @param int $playerWidth
     */
    public function setPlayerWidth($playerWidth);

    /**
     * The twitter:player:height property.
     *
     * @return int
     */
    public function getPlayerHeight();

    /**
     * The twitter:player:height property.
     *
     * @param int $playerHeight
     */
    public function setPlayerHeight($playerHeight);

    /**
     * The twitter:player:stream property.
     *
     * @return string
     */
    public function getPlayerStream();

    /**
     * The twitter:player:stream property.
     *
     * @param string $playerStream
     */
    public function setPlayerStream($playerStream);

    /**
     * The twitter:player:stream:content_type property.
     *
     * @return string
     */
    public function getPlayerStreamContentType();

    /**
     * The twitter:player:stream:content_type property.
     *
     * @param string $playerStreamContentType
     */
    public function setPlayerStreamContentType($playerStreamContentType);
}
