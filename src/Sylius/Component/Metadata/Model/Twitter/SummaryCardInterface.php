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
 * @see https://dev.twitter.com/cards/types/summary
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface SummaryCardInterface extends CardInterface
{
    /**
     * The twitter:creator:id property.
     *
     * @return string
     */
    public function getCreatorId();

    /**
     * The twitter:creator:id property.
     *
     * @param string $creatorId
     */
    public function setCreatorId($creatorId);

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
}
