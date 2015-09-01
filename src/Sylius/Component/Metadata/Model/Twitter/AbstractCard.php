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

use Sylius\Component\Metadata\Model\AbstractMetadata;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class AbstractCard extends AbstractMetadata implements CardInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $site;

    /**
     * @var string
     */
    protected $siteId;

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
    public function getSite()
    {
        return $this->site;
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
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * {@inheritdoc}
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }
}
