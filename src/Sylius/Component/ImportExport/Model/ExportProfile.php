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
    function __construct() 
    {
        $this->reader = 'user_reader';
        $this->readerConfiguration = array();
        $this->writer = 'csv_writer';
        $this->writerConfiguration = array();
        $this->jobs = new ArrayCollection();
    }
}