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

use Prezent\Doctrine\Translatable\TranslatableInterface as BaseTranslatableInterface;
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
     * @var BaseTranslatableInterface
     */
    protected $translatable;

    /**
     * Get the translatable object
     *
     * @return BaseTranslatableInterface
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * Set the translatable object
     *
     * @param BaseTranslatableInterface $translatable
     *
     * @return $this
     */
    public function setTranslatable(BaseTranslatableInterface $translatable = null)
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
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
