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

use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;

/**
 * Separate session bag to store flows data.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SessionFlowsBag extends NamespacedAttributeBag
{
    const STORAGE_KEY = 'sylius.flow.bag';
    const NAME        = 'sylius.flow.bag';

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(self::STORAGE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
