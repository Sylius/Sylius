<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getSlug();
    
    /**
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return array
     */
    public function getParentsSlugs();

    /**
     * @param array $parentsSlugs
     */
    public function setParentsSlugs(array $parentsSlugs);
}
