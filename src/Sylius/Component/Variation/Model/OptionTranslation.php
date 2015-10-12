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

use Sylius\Component\Translation\Model\TranslationInterface;
use Sylius\Component\Translation\Model\TranslationTrait;

/**
 * Product option translation default implementation.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class OptionTranslation implements OptionTranslationInterface, TranslationInterface
{
    use TranslationTrait;

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
