<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Fixture\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

/**
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 */
class ConcreteResourceExtension extends AbstractResourceExtension
{
    protected $configFiles = array();

    protected $configDirectory = '/';
}
