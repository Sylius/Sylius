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

final class Calendar implements DateTimeProvider
{
    /** @var string */
    private $projectDirectory;

    public function __construct(string $projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    public function today(): \DateTimeInterface
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
