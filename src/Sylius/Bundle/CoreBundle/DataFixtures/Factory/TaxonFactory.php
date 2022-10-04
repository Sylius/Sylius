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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\TaxonDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithDescriptionTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithNameTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\TaxonTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\TaxonUpdaterInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<TaxonInterface>
 *
 * @method static TaxonInterface|Proxy createOne(array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TaxonInterface|Proxy find(object|array|mixed $criteria)
 * @method static TaxonInterface|Proxy findOrCreate(array $attributes)
 * @method static TaxonInterface|Proxy first(string $sortedField = 'id')
 * @method static TaxonInterface|Proxy last(string $sortedField = 'id')
 * @method static TaxonInterface|Proxy random(array $attributes = [])
 * @method static TaxonInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] all()
 * @method static TaxonInterface[]|Proxy[] findBy(array $attributes)
 * @method static TaxonInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static TaxonInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method TaxonInterface|Proxy create(array|callable $attributes = [])
 */
class TaxonFactory extends ModelFactory implements TaxonFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use WithDescriptionTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $taxonFactory,
        private RepositoryInterface $taxonRepository,
        private RepositoryInterface $localeRepository,
        private TaxonDefaultValuesInterface $defaultValues,
        private TaxonTransformerInterface $transformer,
        private TaxonUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withSlug(string $slug): self
    {
        return $this->addState(['slug' => $slug]);
    }

    public function withTranslations(array $translations): self
    {
        return $this->addState(['translations' => $translations]);
    }

    public function withChildren(array $children): self
    {
        return $this->addState(['children' => $children]);
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(TaxonInterface $taxon, array $attributes): void
    {
        $this->updater->update($taxon, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transformer->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): TaxonInterface {
                return $this->createTaxon($attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Taxon::class;
    }

    private function createTaxon(array $attributes = []): ?TaxonInterface
    {
        $code = $attributes['code'];

        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);

        if (null === $taxon) {
            /** @var TaxonInterface $taxon */
            $taxon = $this->taxonFactory->createNew();
        }

        $this->updater->update($taxon, $attributes);

        foreach ($attributes['children'] as $childAttributes) {
            $childAttributes['parent'] = $taxon;

            $this::new()
                ->withAttributes($childAttributes)
                ->withoutPersisting()
                ->create();
        }

        return $taxon;
    }
}
