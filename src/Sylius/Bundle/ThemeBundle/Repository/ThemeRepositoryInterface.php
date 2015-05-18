<?php

namespace Sylius\Bundle\ThemeBundle\Repository;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeRepositoryInterface
{
    /**
     * @return ThemeInterface[]
     */
    public function findAll();

    /**
     * @param string $logicalName
     *
     * @return null|ThemeInterface
     */
    public function findByLogicalName($logicalName);
}