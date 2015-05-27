<?php

namespace Sylius\Bundle\ThemeBundle\Translation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Translation\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Translator extends BaseTranslator
{
    /**
     * @var MessageSelector
     */
    protected $selector;

    /**
     * List of all found themes indexed by absoulte paths of them.
     *
     * @var ThemeInterface[]
     */
    protected $themesPathsToThemes;

    /**
     * @var ThemeContextInterface
     */
    protected $themeContext;

    /**
     * @var Collection
     */
    protected $resourcesToThemes;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container, MessageSelector $selector, $loaderIds = array(), array $options = array())
    {
        $this->selector = $selector;

        $themes = $container->get('sylius.repository.theme')->findAll();
        foreach ($themes as $theme) {
            $this->themesPathsToThemes[realpath($theme->getPath())] = $theme;
        }

        $this->themeContext = $container->get('sylius.context.theme');
        $this->resourcesToThemes = new ArrayCollection();

        parent::__construct($container, $selector, $loaderIds, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function addLoader($format, LoaderInterface $loader)
    {
        parent::addLoader($format, new Loader($loader, $this->resourcesToThemes));
    }

    /**
     * {@inheritdoc}
     */
    public function addResource($format, $resource, $locale, $domain = null)
    {
        parent::addResource($format, $resource, $locale, $domain);

        $this->mapResourceToTheme($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->doTranslate(
            $id,
            $parameters,
            $domain,
            $locale,
            null,
            $this->themeContext->getThemesSortedByPriorityInDescendingOrder()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->doTranslate(
            $id,
            $parameters,
            $domain,
            $locale,
            $number,
            $this->themeContext->getThemesSortedByPriorityInDescendingOrder()
        );
    }


    /**
     * @param mixed $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @param integer $number
     * @param ThemeInterface[] $themes
     *
     * @return null|MessageCatalogueInterface
     */
    protected function doTranslate($id, array $parameters, $domain, $locale, $number, array $themes = array())
    {
        $id = (string) $id;
        $domain = $domain ?: 'messages';

        $catalogue = null;
        foreach ($themes as $theme) {
            $themedMessageId = $id . '|' . $theme->getLogicalName();

            if ($catalogue = $this->getCatalogueHavingTranslation($themedMessageId, $domain, $locale)) {
                $id = $themedMessageId;
                break;
            }
        }

        if (null === $catalogue) {
            $catalogue = $this->getCatalogueHavingTranslation($id, $domain, $locale);
        }

        $translatedMessage = $catalogue ? $catalogue->get($id, $domain) : $id;

        if (null !== $number) {
            $translatedMessage = $this->selector->choose($translatedMessage, $number, $catalogue->getLocale());
        }

        return strtr($translatedMessage, $parameters);
    }

    /**
     * @param string $id
     * @param string $domain
     * @param string $locale
     * @return MessageCatalogueInterface|null
     */
    protected function getCatalogueHavingTranslation($id, $domain, $locale)
    {
        $catalogue = $this->getCatalogue($locale);
        while (null !== $catalogue && !$catalogue->defines($id, $domain)) {
            $catalogue = $catalogue->getFallbackCatalogue();
        }

        return $catalogue;
    }

    /**
     * @param string $resource
     */
    private function mapResourceToTheme($resource)
    {
        $resource = realpath($resource);

        foreach ($this->themesPathsToThemes as $themePath => $theme) {
            if (false !== strpos($resource, $themePath)) {
                $this->resourcesToThemes->set($resource, $theme);
                return;
            }
        }

        $this->resourcesToThemes->set($resource, null);
    }
}