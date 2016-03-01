<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Sitemap\Model;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SitemapInterface
{
    /**
     * @return SitemapUrlInterface[]
     */
    public function getUrls();

    /**
     * @param SitemapUrlInterface[] $urlSet
     */
    public function setUrls(array $urlSet);

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
    public function getLocalization();

    /**
     * @param string $localization
     */
    public function setLocalization($localization);

    /**
     * @return \DateTime
     */
    public function getLastModification();

    /**
     * @param \DateTime $lastModification
     */
    public function setLastModification(\DateTime $lastModification);
}
