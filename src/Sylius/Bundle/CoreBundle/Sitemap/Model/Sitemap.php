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

use Sylius\Bundle\CoreBundle\Sitemap\Exception\SitemapUrlNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class Sitemap implements SitemapInterface
{
    /**
     * @var array
     */
    private $urls = [];

    /**
     * @var string
     */
    private $localization;

    /**
     * @var \DateTime
     */
    private $lastModification;

    /**
     * {@inheritdoc}
     */
    public function setUrls(array $urls)
    {
        $this->urls = $urls;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * {@inheritdoc}
     */
    public function addUrl(SitemapUrlInterface $url)
    {
        $this->urls[] = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function removeUrl(SitemapUrlInterface $url)
    {
        $key = array_search($url, $this->urls, true);
        if (false === $key) {
            throw new SitemapUrlNotFoundException($url);
        }

        unset($this->urls[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocalization($localization)
    {
        $this->localization = $localization;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalization()
    {
        return $this->localization;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastModification(\DateTime $lastModification)
    {
        $this->lastModification = $lastModification;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModification()
    {
        return $this->lastModification;
    }
}
