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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Sitemap\Renderer\TemplateAware;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class Sitemap implements SitemapInterface, TemplateAware
{
    /**
     * @var Collection
     */
    private $urlSet;

    /**
     * @var string
     */
    private $loc;

    /**
     * @var \DateTime
     */
    private $lastmod;

    /**
     * @var string
     */
    private $template;

    public function __construct()
    {
        $this->urlSet = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlSet($urlSet)
    {
        $this->urlSet = $urlSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlSet()
    {
        return $this->urlSet;
    }

    /**
     * {@inheritdoc}
     */
    public function addUrl(SitemapUrlInterface $url)
    {
        $this->urlSet->add($url);
    }

    /**
     * {@inheritdoc}
     */
    public function removeUrl(SitemapUrlInterface $url)
    {
        $this->urlSet->removeElement($url);
    }

    /**
     * {@inheritdoc}
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastmod(\DateTime $lastmod)
    {
        $this->lastmod = $lastmod;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
