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

namespace Sylius\Behat\Service\Provider;

use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;
use Sylius\Component\Promotion\Provider\DateTimeProviderInterface;

final class Calendar implements DateTimeProvider, DateTimeProviderInterface
{
    private string $projectDirectory;

    public function __construct(string $projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    public function today(): \DateTimeInterface
    {
        return $this->provideFakeDateIfSet();
    }

    public function now(): \DateTimeInterface
    {
        return $this->provideFakeDateIfSet();
    }

    private function provideFakeDateIfSet(): \DateTimeInterface
    {
        if (file_exists($this->projectDirectory . '/var/temporaryDate.txt')) {
            $file = fopen($this->projectDirectory . '/var/temporaryDate.txt', 'r');
            $dateTime = fgets($file);
            fclose($file);

            return new \DateTimeImmutable($dateTime);
        }

        return new \DateTimeImmutable();
    }
}
