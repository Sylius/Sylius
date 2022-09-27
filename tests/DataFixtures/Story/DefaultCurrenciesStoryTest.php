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

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultCurrenciesStoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultCurrenciesStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_currencies(): void
    {
        /** @var DefaultCurrenciesStoryInterface $defaultCurrenciesStory */
        $defaultCurrenciesStory = self::getContainer()->get('sylius.data_fixtures.story.default_currencies');

        $defaultCurrenciesStory->build();

        $this->assertCurrencyIsOnDatabase('EUR');
        $this->assertCurrencyIsOnDatabase('USD');
        $this->assertCurrencyIsOnDatabase('PLN');
        $this->assertCurrencyIsOnDatabase('CAD');
        $this->assertCurrencyIsOnDatabase('CNY');
        $this->assertCurrencyIsOnDatabase('NZD');
        $this->assertCurrencyIsOnDatabase('GBP');
        $this->assertCurrencyIsOnDatabase('AUD');
        $this->assertCurrencyIsOnDatabase('MXN');
    }

    private function assertCurrencyIsOnDatabase(string $currencyCode)
    {
        /** @var RepositoryInterface $currencyRepository */
        $currencyRepository = self::getContainer()->get('sylius.repository.currency');
        $currency = $currencyRepository->findOneBy(['code' => $currencyCode]);

        $this->assertNotNull($currency, sprintf('Currency "%s" should be on database but it does not.', $currencyCode));
    }
}
