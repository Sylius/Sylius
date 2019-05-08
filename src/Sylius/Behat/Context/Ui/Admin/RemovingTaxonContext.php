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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;

final class RemovingTaxonContext implements Context
{
    /** @var CreatePageInterface */
    private $createPage;

    public function __construct(CreatePageInterface $createPage)
    {
        $this->createPage = $createPage;
    }

    /**
     * @When I remove taxon named :name
     * @When I delete taxon named :name
     */
    public function iRemoveTaxonNamed(string $name): void
    {
        $this->createPage->open();
        $this->createPage->deleteTaxonOnPageByName($name);
    }
}
