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
class ImportProfile extends Profile implements ExportProfileInterface
{
    function __construct()
    {
        $this->reader = 'csv_reader';
        $this->readerConfiguration = array();
        $this->writer = 'product_writer';
        $this->writerConfiguration = array();
        $this->jobs = new ArrayCollection();
    }
}
