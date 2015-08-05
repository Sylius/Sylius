<?php

namespace Sylius\Component\Seo\Model\Twitter;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface SummaryCardInterface
{
    /**
     * The twitter:card property.
     *
     * @return string
     */
    public function getType();

    /**
     * The twitter:site property.
     *
     * @param string $site
     */
    public function setSite($site);

    /**
     * The twitter:site property.
     *
     * @return string
     */
    public function getSite();

    /**
     * The twitter:site:id property.
     *
     * @param string $siteId
     */
    public function setSiteId($siteId);

    /**
     * The twitter:site:id property.
     *
     * @return string
     */
    public function getSiteId();

    /**
     * The twitter:creator:id property.
     *
     * @param string $creatorId
     */
    public function setCreatorId($creatorId);

    /**
     * The twitter:creator:id property.
     *
     * @return string
     */
    public function getCreatorId();

    /**
     * The twitter:title property.
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * The twitter:title property.
     *
     * @return string
     */
    public function getTitle();

    /**
     * The twitter:description property.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * The twitter:description property.
     *
     * @return string
     */
    public function getDescription();

    /**
     * The twitter:image property.
     *
     * @param string $image
     */
    public function setImage($image);

    /**
     * The twitter:image property.
     *
     * @return string
     */
    public function getImage();
}