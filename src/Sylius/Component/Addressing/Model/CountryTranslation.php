<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Sylius\Component\Translation\Model\AbstractTranslation;

/**
 * Default country translation model.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class CountryTranslation extends AbstractTranslation implements CountryTranslationInterface
{
    /**
     * Country id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Country name.
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
