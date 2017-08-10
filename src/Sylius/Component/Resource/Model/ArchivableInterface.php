<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Resource\Model;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface ArchivableInterface
{
    /**
     * @return \DateTimeInterface
     */
    public function getArchivedAt();

    /**
     * @param \DateTimeInterface $archivedAt
     */
    public function setArchivedAt(\DateTimeInterface $archivedAt = null);
}
