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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Covers ProductVariantRepository::iterateCodesOfAllVariants() against a real database.
 *
 * Unit tests can only assert on a mocked repository, so they cannot catch a mismatch between what
 * the interface declares and what Doctrine actually hydrates, nor the ordering that keyset
 * pagination depends on. Both are asserted here.
 */
final class ProductVariantRepositoryTest extends KernelTestCase
{
    /**
     * Declared in the order the fixture inserts them, which is the order the repository must yield.
     * Neither the codes nor the fixture positions follow that order, so ordering the query by `code`
     * or by `position` rather than by the identifier it pages on fails these assertions.
     */
    private const VARIANT_CODES = [
        'ITERATION_VARIANT_GAMMA',
        'ITERATION_VARIANT_ALPHA',
        'ITERATION_VARIANT_ZETA',
        'ITERATION_VARIANT_BETA',
        'ITERATION_VARIANT_OMEGA',
        'ITERATION_VARIANT_DELTA',
        'ITERATION_VARIANT_EPSILON',
    ];

    /** @var ProductVariantRepositoryInterface<ProductVariantInterface> */
    private ProductVariantRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disableOrderByIdentifierWalker();

        /** @var ProductVariantRepositoryInterface<ProductVariantInterface> $repository */
        $repository = self::getContainer()->get('sylius.repository.product_variant');
        $this->repository = $repository;
    }

    /**
     * The test application enables `sylius_core.order_by_identifier`, whose walker appends an ordering
     * by identifier to every query. That would supply the very clause these tests exist to pin, so it
     * is removed here to reproduce a default 2.3 application, where the setting defaults to false.
     */
    private function disableOrderByIdentifierWalker(): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->getConfiguration()->setDefaultQueryHint(Query::HINT_CUSTOM_TREE_WALKERS, []);
    }

    #[Test]
    public function it_yields_every_variant_code_once_in_identifier_order(): void
    {
        $this->loadFixtures();

        // assertSame, not assertEqualsCanonicalizing: keyset pagination is only correct while the
        // query orders by the very column it pages on, so the sequence is part of what is asserted.
        self::assertSame(self::VARIANT_CODES, $this->flatten($this->repository->iterateCodesOfAllVariants(2)));
    }

    #[Test]
    public function it_splits_the_catalog_into_batches_of_at_most_the_requested_size(): void
    {
        $this->loadFixtures();

        $batches = iterator_to_array($this->repository->iterateCodesOfAllVariants(2), false);

        // 7 variants in batches of 2: three full batches then a single-element tail.
        self::assertSame(array_chunk(self::VARIANT_CODES, 2), $batches);
    }

    #[Test]
    public function it_yields_a_single_full_batch_when_the_batch_size_divides_the_catalog_exactly(): void
    {
        $this->loadFixtures();

        // Exercises the path where the last batch is full, so the loop can only end on the
        // following empty result rather than on a short batch.
        $batches = iterator_to_array($this->repository->iterateCodesOfAllVariants(count(self::VARIANT_CODES)), false);

        self::assertSame([self::VARIANT_CODES], $batches);
    }

    #[Test]
    public function it_yields_a_single_batch_when_the_batch_size_exceeds_the_catalog_size(): void
    {
        $this->loadFixtures();

        self::assertSame([self::VARIANT_CODES], iterator_to_array($this->repository->iterateCodesOfAllVariants(1000), false));
    }

    #[Test]
    public function it_yields_one_variant_per_batch_when_the_batch_size_is_one(): void
    {
        $this->loadFixtures();

        self::assertSame(
            array_map(static fn (string $code): array => [$code], self::VARIANT_CODES),
            iterator_to_array($this->repository->iterateCodesOfAllVariants(1), false),
        );
    }

    #[Test]
    public function it_yields_nothing_when_there_are_no_variants(): void
    {
        $this->purgeDatabase();

        self::assertSame([], iterator_to_array($this->repository->iterateCodesOfAllVariants(2), false));
    }

    #[Test]
    #[DataProvider('invalidBatchSizes')]
    public function it_rejects_a_batch_size_below_one_without_waiting_for_iteration(int $batchSize): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Deliberately not wrapped in iterator_to_array: the batch size must be rejected when the
        // method is called, not only once the caller starts iterating.
        $this->repository->iterateCodesOfAllVariants($batchSize);
    }

    /** @return iterable<string, array{int}> */
    public static function invalidBatchSizes(): iterable
    {
        yield 'zero' => [0];
        yield 'negative' => [-1];
        yield 'minimum integer' => [\PHP_INT_MIN];
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
