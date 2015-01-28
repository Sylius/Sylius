<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Translation\Model;


/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AbstractTranslation implements TranslationInterface
{
    /**
     * Locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Translatable object
     *
     * @var TranslatableInterface
     */
    protected $translatable;

    /**
     * {@inheritdoc}
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatable(TranslatableInterface $translatable = null)
    {
        if ($translatable === $this->translatable) {
            return $this;
        }

        $old = $this->translatable;
        $this->translatable = $translatable;

        if (null !== $old) {
            $old->removeTranslation($this);
        }

        if (null !== $translatable) {
            $translatable->addTranslation($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
