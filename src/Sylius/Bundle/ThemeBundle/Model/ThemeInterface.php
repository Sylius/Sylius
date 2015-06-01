<?php

namespace Sylius\Bundle\ThemeBundle\Model;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $logicalName
     */
    public function setLogicalName($logicalName);

    /**
     * @return string
     */
    public function getLogicalName();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getHashCode();
}