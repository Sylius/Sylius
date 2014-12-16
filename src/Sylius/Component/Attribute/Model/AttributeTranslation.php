<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

use Sylius\Component\Translation\Model\AbstractTranslation;

/**
 * Model for object attributes translation.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AttributeTranslation extends AbstractTranslation implements AttributeTranslationInterface
{
    /**
     * Attribute id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Presentation.
     * Displayed to user.
     *
     * @var string
     */
    protected $presentation;

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
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * {@inheritdoc}
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }
}
