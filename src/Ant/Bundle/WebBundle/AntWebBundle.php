<?php
namespace Ant\Bundle\WebBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AntWebBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusWebBundle';
    }
}
