<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\ProductCompare;

use Sylius\Behat\Page\PageInterface;

interface IndexPageInterface extends PageInterface
{
    /**
     * @return mixed
     */
    public function getComparedAttributes();
}
