<?php

declare(strict_types=1);

namespace Sylius\Behat\Application\SyliusTestPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusTestPlugin extends Bundle
{
    use SyliusPluginTrait;
}
