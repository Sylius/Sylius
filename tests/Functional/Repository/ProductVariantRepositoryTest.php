<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Functional\Repository;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Covers ProductVariantRepository::iterateCodesOfAllVariants() against a real database.
 *
 * The unit tests around this method can only assert on a mocked repository, so they cannot catch
 * a mismatch between what the interface declares and what Doctrine actually hydrates. That is
 * exactly how the deprecated getCodesOfAllVariants() came to return a list of ['code' => string]
 * arrays while its annotation promised a list of strings. These tests pin the real shape.
 */
final class ProductVariantRepositoryTest extends KernelTestCase
{
    private const VARIANT_CODES = [
        'ITERATION_VARIANT_1',
        'ITERATION_VARIANT_2',
        'ITERATION_VARIANT_3',
        'ITERATION_VARIANT_4',
        'ITERATION_VARIANT_5',
        'ITERATION_VARIANT_6',
        'ITERATION_VARIANT_7',
    ];

    /** @var ProductVariantRepositoryInterface<ProductVariantInterface> */
    private ProductVariantRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ProductVariantRepositoryInterface<ProductVariantInterface> $repository */
        $repository = self::getContainer()->get('sylius.repository.product_variant');
        $this->repository = $repository;
    }

    #[Test]
    public function it_yields_every_variant_code_exactly_once(): void
    {
        $this->loadFixtures();

        $codes = $this->flatten($this->repository->iterateCodesOfAllVariants(2));

        self::assertEqualsCanonicalizing(self::VARIANT_CODES, $codes);
        self::assertSame(count(self::VARIANT_CODES), count($codes), 'No variant should be yielded twice.');
    }

    #[Test]
    public function it_yields_codes_as_plain_strings(): void
    {
        $this->loadFixtures();

        foreach ($this->repository->iterateCodesOfAllVariants(2) as $batch) {
            self::assertNotEmpty($batch);

            foreach ($batch as $code) {
                self::assertIsString($code);
            }
        }
    }

    #[Test]
    public function it_splits_the_catalog_into_batches_of_at_most_the_requested_size(): void
    {
        $this->loadFixtures();

        $batches = iterator_to_array($this->repository->iterateCodesOfAllVariants(2), false);

        // 7 variants in batches of 2 gives 4 batches, the last one holding a single variant.
        self::assertCount(4, $batches);
        self::assertSame([2, 2, 2, 1], array_map('count', $batches));
    }

    #[Test]
    public function it_yields_a_single_batch_when_the_batch_size_exceeds_the_catalog_size(): void
    {
        $this->loadFixtures();

        $batches = iterator_to_array($this->repository->iterateCodesOfAllVariants(1000), false);

        self::assertCount(1, $batches);
        self::assertEqualsCanonicalizing(self::VARIANT_CODES, $batches[0]);
    }

    #[Test]
    public function it_yields_one_variant_per_batch_when_the_batch_size_is_one(): void
    {
        $this->loadFixtures();

        $batches = iterator_to_array($this->repository->iterateCodesOfAllVariants(1), false);

        self::assertCount(count(self::VARIANT_CODES), $batches);
        self::assertSame([1, 1, 1, 1, 1, 1, 1], array_map('count', $batches));
    }

    #[Test]
    public function it_yields_nothing_when_there_are_no_variants(): void
    {
        $this->purgeDatabase();

        self::assertSame([], iterator_to_array($this->repository->iterateCodesOfAllVariants(2), false));
    }

    #[Test]
    public function it_rejects_a_batch_size_below_one_without_waiting_for_iteration(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Not wrapped in iterator_to_array on purpose: the batch size must be rejected when the
        // method is called, not only once the caller starts iterating.
        $this->repository->iterateCodesOfAllVariants(0);
    }

    /**
     * @param iterable<list<string>> $batches
     *
     * @return list<string>
     */
    private function flatten(iterable $batches): array
    {
        $codes = [];

        foreach ($batches as $batch) {
            foreach ($batch as $code) {
                $codes[] = $code;
            }
        }

        return $codes;
    }

    private function loadFixtures(): void
    {
        $this->getFixtureLoader()->load(
            [__DIR__ . '/../../DataFixtures/ORM/resources/product_variants_for_iteration.yml'],
            [],
            [],
            PurgeMode::createDeleteMode(),
        );
    }

    private function purgeDatabase(): void
    {
        $this->getFixtureLoader()->load([], [], [], PurgeMode::createDeleteMode());
    }

    private function getFixtureLoader(): LoaderInterface
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        return $fixtureLoader;
    }
}
