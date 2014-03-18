<?php

namespace Sylius\Bundle\CoreBundle\Releaser;

interface ReleaserInterface
{
    /**
     * Release all expired orders.
     *
     * @return Boolean
     */
    public function release();
}
