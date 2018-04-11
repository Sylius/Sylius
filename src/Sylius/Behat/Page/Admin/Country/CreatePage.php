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
    public function addPostCode(string $postCode, string $name): void
    {
        $this->getDocument()->clickLink('Add postcode');

        $postCodeForm = $this->getLastPostCodeElement();

        $postCodeForm->fillField('Post code', $postCode);
        $postCodeForm->fillField('Name', $name);
    }

    /**
     *{@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'provinces' => '#sylius_country_provinces',
            'postCodes' => '#sylius_country_postCodes',
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
        $postCodes = $this->getElement('postCodes');
        $items = $postCodes->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
