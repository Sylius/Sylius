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
 * {@inheritdoc}
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SummaryCard extends AbstractCard implements SummaryCardInterface
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'summary';

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
    public function serialize()
    {
        return json_encode([
            $this->site,
            $this->siteId,
            $this->creatorId,
            $this->title,
            $this->description,
            $this->image,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->site,
            $this->siteId,
            $this->creatorId,
            $this->title,
            $this->description,
            $this->image,
        ) = json_decode($serialized, true);
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