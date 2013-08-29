<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Storage;

/**
 * Base storage class.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Storage implements StorageInterface
{
    /**
     * Storage domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * {@inheritdoc}
     */
    public function initialize($domain)
    {
        $this->domain = $domain;
    }
}
