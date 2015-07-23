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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ExportProfile extends Profile implements ExportProfileInterface
{
    const DEFAULT_READER = 'user_orm';
    const DEFAULT_WRITER = 'csv_writer';

    function __construct()
    {
        $this->reader = ExportProfile::DEFAULT_READER;
        $this->readerConfiguration = array();
        $this->writer = ExportProfile::DEFAULT_WRITER;
        $this->writerConfiguration = array();
        $this->jobs = new ArrayCollection();
    }
}
