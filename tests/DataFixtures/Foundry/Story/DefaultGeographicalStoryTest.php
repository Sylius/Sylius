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

namespace Sylius\Tests\DataFixtures\Foundry\Story;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultGeographicalStory;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultGeographicalStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_countries(): void
    {
        self::bootKernel();

        DefaultGeographicalStory::load();

        $countries = $this->getCountryRepository()->findAll();

        $this->assertCount(12, $countries);

        foreach ($this->getExpectedCountryCodes() as $code) {
            $this->assertCountryCodeExists($code);
        }
    }

    private function getExpectedCountryCodes(): array
    {
        return [
            'US',
            'FR',
            'DE',
            'AU',
            'CA',
            'MX',
            'NZ',
            'PT',
            'ES',
            'CN',
            'GB',
            'PL',
        ];
    }

    private function assertCountryCodeExists(string $code): void
    {
        $currency = $this->getCountryRepository()->findOneBy(['code' => $code]);
        $this->assertNotNull($currency, sprintf('Country %s was not found.', $code));
    }

    private function getCountryRepository(): RepositoryInterface
    {
        return self::getContainer()->get('sylius.repository.country');
    }
}
