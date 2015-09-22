<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Seo\Model;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataSubjectInterface
{
    /**
     * @return string
     */
    public function getMetadataIdentifier();
}