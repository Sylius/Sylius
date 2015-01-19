<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Exception\Driver;

use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class UnknownDriverException extends \Exception
{
    public function __construct(ResourceMetadataInterface $metadata)
    {
        parent::__construct(sprintf(
            'Unknown driver "%s" for resource "%s".',
            $metadata->getDriver(),
            $metadata->getAlias()
        ));
    }
}
