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

use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;

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
     * Get the translatable object
     *
     * @return TranslatableInterface
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * Set the translatable object
     *
     * @param TranslatableInterface $translatable
     * @return self
     */
    public function setTranslatable(TranslatableInterface $translatable = null)
    {
        if ($this->translatable == $translatable) {
            return $this;
        }

        $old = $this->translatable;
        $this->translatable = $translatable;

        if ($old !== null) {
            $old->removeTranslation($this);
        }

        if ($translatable !== null) {
            $translatable->addTranslation($this);
        }

        return $this;
    }

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale
     *
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}