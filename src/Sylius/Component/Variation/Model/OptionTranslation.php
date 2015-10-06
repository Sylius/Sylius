<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Sylius\Component\Translation\Model\AbstractTranslation;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class OptionTranslation extends AbstractTranslation implements OptionTranslationInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
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
    }
}
