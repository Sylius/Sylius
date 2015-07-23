<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImportProfile extends Profile implements ImportProfileInterface
{
    const DEFAULT_READER = 'csv_reader';
    const DEFAULT_WRITER = 'user_orm';

    function __construct()
    {
        $this->reader = ImportProfile::DEFAULT_READER;
        $this->readerConfiguration = array();
        $this->writer = ImportProfile::DEFAULT_WRITER;
        $this->writerConfiguration = array();
        $this->jobs = new ArrayCollection();
    }
}
