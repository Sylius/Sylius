<?php
namespace Ant\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AntCoreBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusCoreBundle';
    }
}
