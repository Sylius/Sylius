<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Exception;

/**
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class MissingBundleException extends \RuntimeException
{
    /**
     * @param string $bundle
     */
    public function __construct($bundle)
    {
        parent::__construct(sprintf('Bundle "%s" is needed. Please enable it in your AppKernel.', $bundle));
    }
}
