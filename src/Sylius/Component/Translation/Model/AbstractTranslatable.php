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

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractTranslatable implements TranslatableInterface
{
    /**
     * Translations
     *
     * @var ArrayCollection[TranslationInterface]
     */
    protected $translations;

    /**
     * Current locale
     *
     * @var string
     */
    protected $currentLocale;

    /**
     * Cache current translation. Useful in Doctrine 2.4+
     *
     * @var string
     */
    protected $currentTranslation;

    /**
     * Fallback locale
     *
     * @var string
     */
    protected $fallbackLocale;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Get the translations
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add a translation
     *
     * @param TranslationInterface $translation
     * @return self
     */
    public function addTranslation(TranslationInterface $translation)
    {
        if (!$this->translations->containsKey($translation->getLocale())) {
            $this->translations->set($translation->getLocale(), $translation);
            $translation->setTranslatable($this);
        }

        return $this;
    }

    /**
     * Remove a translation
     *
     * @param TranslationInterface $translation
     * @return self
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setTranslatable(null);
        }

        return $this;
    }

    /**
     * @param TranslationInterface $translation
     *
     * @return bool
     */
    public function hasTranslation(TranslationInterface $translation)
    {
        return $this->translations->containsKey($translation->getLocale());
    }

    /**
     * @param string $currentLocale
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    /**
     * @param TranslationInterface $currentTranslation
     */
    public function setCurrentTranslation(TranslationInterface $currentTranslation)
    {
        $this->currentTranslation = $currentTranslation;

        return $this;
    }

    /**
     * @return TranslationInterface
     */
    public function getCurrentTranslation()
    {
        return $this->currentTranslation;
    }

    /**
     * @param string $fallbackLocale
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this->fallbackLocale = $fallbackLocale;

        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * Translation helper method
     *
     * @param string locale
     *
     * @throws \RuntimeException
     * @return TranslationInterface
     */
    public function translate($locale = null)
    {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }

        if (!$locale) {
            throw new \RuntimeException('No locale has been set and currentLocale is empty');
        }

        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }

        // TODO Throw exception? Get default translation?
        if (!$translation = $this->translations->get($locale)) {
            if (!$fallbackTranslation = $this->translations->get($this->getFallbackLocale())) {
                $className = $this->getTranslationEntityClass();
                $translation = new $className();
                $translation->setLocale($locale);
                $this->addTranslation($translation);
            } else {
                $translation = clone $fallbackTranslation;
            }
        }

        $this->currentTranslation = $translation;

        return $translation;
    }

    /**
     * Description of method
     *
     * @return string
     */
    protected function getTranslationEntityClass()
    {
        return get_class($this).'Translation';
    }
}
