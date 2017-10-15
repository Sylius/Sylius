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

namespace Sylius\Component\Core\Exception;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class MissingChannelConfigurationException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $message, ?\Exception $previousException = null)
    {
        parent::__construct($message, 0, $previousException);
    }
}
