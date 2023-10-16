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

namespace Sylius\Bundle\ShopBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final class ProductGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    private ?Request $resquest;

    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private string $resourceClass,
        private ChannelContextInterface $channelContext,
        private TaxonRepositoryInterface $taxonRepository,
        private LocaleContextInterface $localeContext,
        RequestStack $requestStatck,
        private bool  $includeAllDescendants,
    ) {
        $this->resquest = $requestStatck->getMainRequest();
    }

    public static function getName(): string
    {
        return 'sylius_shop_product';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        Assert::notNull($this->resquest);
        $localeCode = $this->localeContext->getLocaleCode();

        // @see Sylius\Bundle\ResourceBundle\ExpressionLanguage\NotNullExpressionFunctionProvider
        $taxon = $this->taxonRepository->findOneBySlug(
                    $this->resquest->attributes->get('slug'),
                    $localeCode,
        );
        if ($taxon === null)  {
            throw new NotFoundHttpException('Requested page is invalid');
        }

        $gridBuilder
            ->setRepositoryMethod('createShopListQueryBuilder', [
                $this->channelContext->getChannel(),
                $taxon,
                $localeCode,
                $this->resquest->query->all('sorting'),
                $this->includeAllDescendants,
            ])
            ->orderBy('position', 'asc')
            ->setLimits([
                9,
                18,
                27,
            ])
            ->addField(
                DateTimeField::create('createdAt')
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('position')
                    ->setSortable(true, 'productTaxon.position'),
            )
            ->addField(
                StringField::create('name')
                    ->setSortable(true, 'translation.name'),
            )
            ->addField(
                Field::create('price', 'int')
                    ->setSortable(true, 'channelPricing.price'),
            )
            ->addFilter(
                StringFilter::create('search', ['translation.name'])
                    ->setLabel(false)
                    ->addFormOption('type', 'contains')
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
