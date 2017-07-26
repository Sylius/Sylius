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
 * @see ArchivableInterface
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
trait ArchivableTrait
{
    /**
     * @var \DateTime|null
     */
    protected $archivedAt;

    /**
     * @return \DateTime|null
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * @param \DateTime|null $archivedAt
     */
    public function setArchivedAt(\DateTime $archivedAt = null)
    {
        $this->archivedAt = $archivedAt;
    }
}
