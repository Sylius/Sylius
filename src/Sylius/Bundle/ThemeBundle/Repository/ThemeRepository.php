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
    /**
     * @param array|ThemeInterface[] $themes Can be an array of serialized themes
     */
    public function __construct(array $themes = [])
    {
        parent::__construct(ThemeInterface::class);

        foreach ($themes as $theme) {
            if (!$theme instanceof ThemeInterface) {
                $theme = unserialize($theme);
            }

            $this->add($theme);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->findOneBySlug($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy(['slug' => $slug]);
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
