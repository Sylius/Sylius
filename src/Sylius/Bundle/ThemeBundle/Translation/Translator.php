<?php

namespace Sylius\Bundle\ThemeBundle\Translation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Translation\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Translator extends BaseTranslator
{
    /**
     * @var ThemeInterface[]
     */
    protected $themes;

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
        $this->themes = $container->get('sylius.repository.theme')->findAll();
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
        foreach ($this->themeContext->getThemesSortedByPriorityInDescendingOrder() as $theme) {
            $themedMessageId = $id . '|' . $theme->getLogicalName();
            $translatedMessage = parent::trans($themedMessageId, $parameters, $domain, $locale);

            if ($themedMessageId !== $translatedMessage) {
                return $translatedMessage;
            }
        }

        return parent::trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string $resource
     */
    private function mapResourceToTheme($resource)
    {
        $resource = realpath($resource);

        foreach ($this->themes as $theme) {
            if (false !== strpos($resource, realpath($theme->getPath()))) {
                $this->resourcesToThemes->set($resource, $theme);
                return;
            }
        }

        $this->resourcesToThemes->set($resource, null);
    }
}