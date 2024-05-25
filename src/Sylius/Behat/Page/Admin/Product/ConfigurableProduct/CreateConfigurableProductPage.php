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

namespace Sylius\Behat\Page\Admin\Product\ConfigurableProduct;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Page\Admin\Product\Common\ProductAttributesTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductMediaTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductTaxonomyTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductTranslationsTrait;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class CreateConfigurableProductPage extends BaseCreatePage implements CreateConfigurableProductPageInterface
{
    use ConfigurableProductFormTrait;
    use ProductAttributesTrait;
    use ProductMediaTrait;
    use ProductTaxonomyTrait;
    use ProductTranslationsTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function create(): void
    {
        $this->waitForFormUpdate();

        parent::create();
    }

    /**
     * @return string[]
     */
    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            $this->getDefinedFormElements(),
            $this->getDefinedProductMediaElements(),
            $this->getDefinedProductAttributesElements(),
            $this->getDefinedProductTranslationsElements(),
            $this->getDefinedProductTaxonomyElements(),
        );
    }
}
