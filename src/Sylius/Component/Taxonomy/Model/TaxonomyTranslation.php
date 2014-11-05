<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Model;
use Sylius\Component\Translation\Model\AbstractTranslation;

/**
 * Model for taxonomy translations.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonomyTranslation extends AbstractTranslation implements TaxonomyTranslationInterface
{
    /**
     * Taxonomy id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Taxonomy name.
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
