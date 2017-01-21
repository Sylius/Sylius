<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface ArchivableInterface
{
    /**
     * @return \DateTime
     */
    public function getArchivedAt();

    /**
     * @param \DateTime $archivedAt
     */
    public function setArchivedAt(\DateTime $archivedAt = null);
}
