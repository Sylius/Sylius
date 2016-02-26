<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Repository;

use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Component\Resource\Repository\InMemoryRepository;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class InMemoryThemeRepository extends InMemoryRepository implements ThemeRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param ThemeLoaderInterface $themeLoader
     */
    public function __construct(ThemeLoaderInterface $themeLoader, $interface)
    {
        parent::__construct($interface);

        $themes = $themeLoader->load();
        foreach ($themes as $theme) {
            $this->add($theme);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
