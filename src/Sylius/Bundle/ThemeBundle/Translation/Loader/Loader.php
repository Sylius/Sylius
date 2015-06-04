<?php

namespace Sylius\Bundle\ThemeBundle\Translation\Loader;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class Loader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var Collection|ThemeInterface[]|null[]
     */
    protected $resourcesToThemes;

    /**
     * @param LoaderInterface $loader
     * @param Collection $resourcesToThemes
     */
    public function __construct(LoaderInterface $loader, Collection $resourcesToThemes)
    {
        $this->loader = $loader;
        $this->resourcesToThemes = $resourcesToThemes;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $messageCatalogue = $this->loader->load($resource, $locale, $domain);

        if (null !== $theme = $this->resourcesToThemes->get(realpath($resource))) {
            $messages = $messageCatalogue->all($domain);

            foreach ($messages as $key => $value) {
                unset($messages[$key]);
                $messages[$key . '|' . $theme->getLogicalName()] = $value;
            }

            $messageCatalogue->replace($messages, $domain);
        }

        return $messageCatalogue;
    }
}