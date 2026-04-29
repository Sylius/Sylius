<?php

namespace Sylius\Bundle\AdminBundle\Menu\Provider;

use Knp\Menu\ItemInterface;

interface MenuProviderInterface
{
    public function __invoke(ItemInterface $menu): void;
}
