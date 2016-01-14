<?php

namespace Sylius\Component\Resource\Model;

/**
 * @author Richard van Laak <rvanlaak@gmail.com>
 */
trait ToggleableTrait
{

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = (bool) $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $this->enabled = false;
    }
}
