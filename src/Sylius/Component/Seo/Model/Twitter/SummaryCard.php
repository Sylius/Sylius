<?php

namespace Sylius\Component\Seo\Model\Twitter;

/**
 * @see https://dev.twitter.com/cards/types/summary
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SummaryCard implements SummaryCardInterface
{
    /**
     * @var string
     */
    protected $type = 'summary';

    /**
     * @var string
     */
    protected $site;

    /**
     * @var string
     */
    protected $siteId;

    /**
     * @var string
     */
    protected $creatorId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $image;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * {@inheritdoc}
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->image;
    }
}