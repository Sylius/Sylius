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

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\IndexPage as CrudIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\Checker\ImageExistenceCheckerInterface;
use Symfony\Component\Routing\RouterInterface;

final class IndexPage extends CrudIndexPage implements IndexPageInterface
{
    /** @var ImageExistenceCheckerInterface */
    private $imageExistenceChecker;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        string $routeName,
        ImageExistenceCheckerInterface $imageExistenceChecker
    ) {
        parent::__construct($session, $minkParameters, $router, $tableAccessor, $routeName);

        $this->imageExistenceChecker = $imageExistenceChecker;
    }

    public function filterByTaxon(string $taxonName): void
    {
        $this->getElement('taxon_filter', ['%taxon%' => $taxonName])->click();
    }

    public function hasProductAccessibleImage(string $productCode): bool
    {
        $productRow = $this->getTableAccessor()->getRowWithFields($this->getElement('table'), ['code' => $productCode]);
        $imageUrl = $productRow->find('css', 'img')->getAttribute('src');

        return $this->imageExistenceChecker->doesImageWithUrlExist($imageUrl, 'sylius_admin_product_thumbnail');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'taxon_filter' => '.item a:contains("%taxon%")',
        ]);
    }
}
