<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Model;

/**
 * Interface for Sequence subjects, like Order, Invoice, etc.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface SequenceSubjectInterface
{
    /**
     * @return string
     */
    public function getSequenceType();

    /**
     * @return string|null
     */
    public function getNumber();

    /**
     * @param string
     */
    public function setNumber($number);
}
