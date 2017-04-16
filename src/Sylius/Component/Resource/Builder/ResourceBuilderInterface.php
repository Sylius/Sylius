<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource;

interface ResourceBuilderInterface
{
    /**
     * @param array $data
     *
     * @return $this
     */
    public function create(array $data = array());

    /**
     * @return object
     */
    public function get();

    /**
     * @param bool $flush
     *
     * @return object
     */
    public function save($flush = true);
} 