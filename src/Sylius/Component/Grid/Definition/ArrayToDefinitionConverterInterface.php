<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Definition;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ArrayToDefinitionConverterInterface
{
    /**
     * @param string $code
     * @param array $configuration
     *
     * @return Grid
     */
    public function convert($code, array $configuration);
}
