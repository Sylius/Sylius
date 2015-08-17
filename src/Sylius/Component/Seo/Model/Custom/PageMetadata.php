<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Seo\Model\Custom;

use Sylius\Component\Seo\Model\AbstractMetadata;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadata extends AbstractMetadata implements PageMetadataInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string[]
     */
    protected $keywords = [];

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return json_encode([
            $this->title,
            $this->description,
            $this->keywords,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->title,
            $this->description,
            $this->keywords,
        ) = json_decode($serialized, true);
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
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}