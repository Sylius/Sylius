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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SitemapUrlInterface
{
    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';

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

    /**
     * @return string
     */
    public function getChangefreq();

    /**
     * @param string $changefreq
     */
    public function setChangefreq($changefreq);

    /**
     * @return float
     */
    public function getPriority();

    /**
     * @param float $priority
     */
    public function setPriority($priority);
}
