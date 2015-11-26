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

use Sylius\Bundle\CoreBundle\Sitemap\Renderer\TemplateAware;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapUrl implements SitemapUrlInterface, TemplateAware
{
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
    private $changefreq;

    /**
     * @var float
     */
    private $priority;

    /**
     * @var string
     */
    private $template;

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
    public function setLoc($loc)
    {
        $this->loc = $loc;
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
    public function setLastmod(\DateTime $lastmod)
    {
        $this->lastmod = $lastmod;
    }

    /**
     * {@inheritdoc}
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * {@inheritdoc}
     */
    public function setChangefreq($changefreq)
    {
        if (!in_array($changefreq, self::getSupportedChangefreq(), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The value %s is not supported by the option changefreq.',
                    $changefreq
                )
            );
        }

        $this->changefreq = $changefreq;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        if (!is_numeric($priority) || 0 >= $priority || 1 <= $priority) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The value %s is not supported by the option priority, it must be a numeric between 0.0 and 1.0.',
                    $priority
                )
            );
        }

        $this->priority = $priority;
    }

    static function getSupportedChangefreq()
    {
        $class = new \ReflectionClass(__CLASS__);

        return $class->getConstants();
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
