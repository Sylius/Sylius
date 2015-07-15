<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Sylius\Component\Translation\Model\AbstractTranslation;

/**
 * Shipping method translation model.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ShippingMethodTranslation extends AbstractTranslation implements ShippingMethodTranslationInterface
{
    /**
     * Shipping method identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
