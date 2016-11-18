<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @see TranslatableInterface
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
trait TranslatableTrait
{
    /**
     * @var TranslationInterface[]
     */
    protected $translations;

    /**
     * @var string
     */
    protected $currentLocale;

    /**
     * Cache current translation. Useful in Doctrine 2.4+
     *
     * @var TranslationInterface
     */
    protected $currentTranslation;

    /**
     * @var string
     */
    protected $fallbackLocale;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @param string $locale
     *
     * @return TranslationInterface
     */
    public function getTranslation($locale = null)
    {
        $locale = $locale ?: $this->currentLocale;
        if (null === $locale) {
            throw new \RuntimeException('No locale has been set and current locale is undefined.');
        }

        $translation = $this->translations->get($locale);
        if (null !== $translation) {
            return $translation;
        }

        $fallbackTranslation = $this->translations->get($this->fallbackLocale);
        if (null !== $fallbackTranslation) {
            return $fallbackTranslation;
        }

        $translation = $this->createTranslation();
        $translation->setLocale($locale);

        $this->addTranslation($translation);

        return $translation;
    }

    /**
     * @return TranslationInterface[]
     */
    public function getTranslations()
    {
        return $this->translations;
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
     * @param TranslationInterface $translation
     */
    public function addTranslation(TranslationInterface $translation)
    {
        if (!$this->translations->containsKey($translation->getLocale())) {
            $this->translations->set($translation->getLocale(), $translation);
            $translation->setTranslatable($this);
        }
    }

    /**
     * @param TranslationInterface $translation
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setTranslatable(null);
        }
    }

    /**
     * @param string $currentLocale
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;
    }

    /**
     * @param string $fallbackLocale
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * Create resource translation model.
     *
     * @return TranslationInterface
     */
    abstract protected function createTranslation();
}
