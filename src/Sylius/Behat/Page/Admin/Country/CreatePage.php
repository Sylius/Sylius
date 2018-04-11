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

namespace Sylius\Behat\Page\Admin\Country;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChoosesName;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChoosesName;

    /**
     * {@inheritdoc}
     */
    public function addProvince(string $name, string $code, ?string $abbreviation = null): void
    {
        $this->getDocument()->clickLink('Add province');

        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Name', $name);
        $provinceForm->fillField('Code', $code);

        if (null !== $abbreviation) {
            $provinceForm->fillField('Abbreviation', $abbreviation);
        }
    }

    /** {@inheritdoc} */
    public function addPostCode(string $postcode, string $name): void
    {
        $this->getDocument()->clickLink('Add postcode');

        $postcodeForm = $this->getLastPostCodeElement();

        $postcodeForm->fillField('Post code', $postcode);
        $postcodeForm->fillField('Name', $name);
    }

    /**
     *{@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'provinces' => '#sylius_country_provinces',
            'postcodes' => '#sylius_country_postcodes',
        ]);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getLastProvinceElement(): NodeElement
    {
        $provinces = $this->getElement('provinces');
        $items = $provinces->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getLastPostCodeElement(): NodeElement
    {
        $postcodes = $this->getElement('postcodes');
        $items = $postcodes->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
