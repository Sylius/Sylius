<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Translation;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
    public function __call(string $method, array $arguments)
    {
        $translator = $this->translator;
        $arguments = array_values($arguments);

        return $translator->$method(...$arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $this->transformLocale($locale));
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->translator->transChoice($id, $number, $parameters, $domain, $this->transformLocale($locale));
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale): void
    {
        $this->translator->setLocale($this->transformLocale($locale));
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalogue($locale = null): MessageCatalogueInterface
    {
        return $this->translator->getCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir): void
    {
        if ($this->translator instanceof WarmableInterface) {
            $this->translator->warmUp($cacheDir);
        }
    }

    /**
     * @param string|null $locale
     *
     * @return string|null
     */
    private function transformLocale(?string $locale): ?string
    {
        $theme = $this->themeContext->getTheme();

        if (null === $theme) {
            return $locale;
        }

        if (null === $locale) {
            $locale = $this->getLocale();
        }

        return $locale . '@' . str_replace('/', '-', $theme->getName());
    }
}
