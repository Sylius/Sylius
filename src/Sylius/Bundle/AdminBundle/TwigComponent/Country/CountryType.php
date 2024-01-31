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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Country;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryType as CountryTypeForm;
use Sylius\Bundle\AdminBundle\TwigComponent\UiEventsTrait;
use Sylius\Component\Addressing\Model\Country;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(name: 'SyliusAdmin.Country.CountryType', template: '@SyliusAdmin/Country/Component/countryType.html.twig')]
final class CountryType
{
    use DefaultActionTrait;
    use LiveCollectionTrait;
    use UiEventsTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?Country $country = null;

    #[ExposeInTemplate(name: 'is_update')]
    public bool $isUpdate = false;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(CountryTypeForm::class, $this->country);
    }
}
