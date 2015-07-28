<?php

namespace Sylius\Component\Resource\Model;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ToggleableInterface
{
    /**
     * @return boolean
     */
    public function isEnabled();

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    public function enable();

    public function disable();
}