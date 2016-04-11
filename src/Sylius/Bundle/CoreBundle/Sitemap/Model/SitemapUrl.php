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
class SitemapUrl implements SitemapUrlInterface
{
    /**
     * @var string
     */
    private $localization;

    /**
     * @var \DateTime
     */
    private $lastModification;

    /**
     * @var ChangeFrequency
     */
    private $changeFrequency;

    /**
     * @var float
     */
    private $priority;

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
    public function setLocalization($localization)
    {
        $this->localization = $localization;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModification()
    {
        return $this->lastModification;
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
    public function getChangeFrequency()
    {
        return (string) $this->changeFrequency;
    }

    /**
     * {@inheritdoc}
     */
    public function setChangeFrequency(ChangeFrequency $changeFrequency)
    {
        $this->changeFrequency = $changeFrequency;
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
        if (!is_numeric($priority) || 0 > $priority || 1 < $priority) {
            throw new \InvalidArgumentException(sprintf(
                'The value %s is not supported by the option priority, it must be a numeric between 0.0 and 1.0.', $priority
            ));
        }

        $this->priority = $priority;
    }
}
