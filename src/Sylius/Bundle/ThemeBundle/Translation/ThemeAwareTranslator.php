<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeAwareTranslator implements TranslatorInterface, TranslatorBagInterface, WarmableInterface
{
    /**
     * @var TranslatorInterface|TranslatorBagInterface
     */
    private $translator;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * {@inheritdoc}
     */
    public function __construct(TranslatorInterface $translator, ThemeContextInterface $themeContext)
    {
        if (!$translator instanceof TranslatorBagInterface) {
            throw new \InvalidArgumentException(sprintf(
                'The Translator "%s" must implement TranslatorInterface and TranslatorBagInterface.',
                get_class($translator)
            ));
        }

        $this->translator = $translator;
        $this->themeContext = $themeContext;
    }

    /**
     * Passes through all unknown calls onto the translator object.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        $translator = $this->translator;
        $arguments = array_values($arguments);

        return $translator->$method(...$arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->translator->trans($id, $parameters, $domain, $this->transformLocale($locale));
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->translator->transChoice($id, $number, $parameters, $domain, $this->transformLocale($locale));
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($this->transformLocale($locale));
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalogue($locale = null)
    {
        return $this->translator->getCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        if ($this->translator instanceof WarmableInterface) {
            $this->translator->warmUp($cacheDir);
        }
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    private function transformLocale($locale)
    {
        $theme = $this->themeContext->getTheme();

        if (null === $theme) {
            return $locale;
        }

        if (null === $locale) {
            $locale = $this->getLocale();
        }

        $locale = $locale . '@' . str_replace('/', '-', $theme->getName());

        return $locale;
    }
}
