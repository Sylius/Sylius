<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductAssociationTypeTranslation extends AbstractTranslation implements ProductAssociationTypeTranslationInterface
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var string
     */
    private $name;

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
    }
}
