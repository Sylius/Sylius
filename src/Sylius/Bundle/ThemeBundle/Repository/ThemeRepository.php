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

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Resource\Repository\InMemoryRepository;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeRepository extends InMemoryRepository implements ThemeRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(ThemeInterface::class);
    }

    /**
     * @param string $serializedTheme
     */
    public function addSerialized($serializedTheme)
    {
        $this->add(unserialize($serializedTheme));
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->findOneByName($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByPath($path)
    {
        /** @var ThemeInterface $theme */
        foreach ($this->findAll() as $theme) {
            if (0 === strpos($path, $theme->getPath())) {
                return $theme;
            }
        }

        return null;
    }
}
