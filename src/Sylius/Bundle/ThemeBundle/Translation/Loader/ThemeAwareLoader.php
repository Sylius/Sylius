<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Loader;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeAwareLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @param LoaderInterface $loader
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(LoaderInterface $loader, ThemeRepositoryInterface $themeRepository)
    {
        $this->loader = $loader;
        $this->themeRepository = $themeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $messageCatalogue = $this->loader->load($resource, $locale, $domain);

        $theme = $this->themeRepository->findOneByPath($resource);
        if (null !== $theme) {
            $messages = $messageCatalogue->all($domain);

            foreach ($messages as $key => $value) {
                unset($messages[$key]);
                $messages[$key . '|' . $theme->getName()] = $value;
            }

            $messageCatalogue->replace($messages, $domain);
        }

        return $messageCatalogue;
    }
}
