<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Sitemap\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SitemapInterface
{
    /**
     * @return string
     */
    public function getUrlSet();

    /**
     * @param Collection $urlSet
     */
    public function setUrlSet($urlSet);

    /**
     * @param SitemapUrlInterface $url
     */
    public function addUrl(SitemapUrlInterface $url);

    /**
     * @param SitemapUrlInterface $url
     */
    public function removeUrl(SitemapUrlInterface $url);

    /**
     * @return string
     */
    public function getLoc();

    /**
     * @param string $loc
     */
    public function setLoc($loc);

    /**
     * @return \DateTime
     */
    public function getLastmod();

    /**
     * @param \DateTime $lastmod
     */
    public function setLastmod(\DateTime $lastmod);
}
