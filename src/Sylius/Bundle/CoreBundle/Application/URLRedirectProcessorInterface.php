<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 11:09
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Application;

interface URLRedirectProcessorInterface
{
    public function redirectRoute(string $oldRoute);
}