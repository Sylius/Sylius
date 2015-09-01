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

use Sylius\Component\Metadata\Model\MetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CardInterface extends MetadataInterface
{
    /**
     * The twitter:card property.
     *
     * @return string
     */
    public function getType();

    /**
     * The twitter:site property.
     *
     * @return string
     */
    public function getSite();

    /**
     * The twitter:site property.
     *
     * @param string $site
     */
    public function setSite($site);

    /**
     * The twitter:site:id property.
     *
     * @return string
     */
    public function getSiteId();

    /**
     * The twitter:site:id property.
     *
     * @param string $siteId
     */
    public function setSiteId($siteId);
}
