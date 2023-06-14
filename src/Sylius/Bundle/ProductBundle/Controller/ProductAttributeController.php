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

namespace Sylius\Bundle\ProductBundle\Controller;

use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeChoiceType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductAttribute;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductAttributeController extends ResourceController
{
    public function getAttributeTypesAction(Request $request, string $template): Response
    {
        return $this->render(
            $template,
            [
                'types' => $this->get('sylius.registry.attribute_type')->all(),
                'metadata' => $this->metadata,
            ],
        );
    }

    public function renderAttributesAction(Request $request): Response
    {
        $template = $request->attributes->get('template', '@SyliusAttribute/attributeChoice.html.twig');

        $form = $this->get('form.factory')->create(ProductAttributeChoiceType::class, null, [
            'multiple' => true,
        ]);

        return $this->render($template, ['form' => $form->createView()]);
    }

    public function renderAttributeValueFormsAction(Request $request): Response
    {
        $template = $request->attributes->get('template', '@SyliusAttribute/attributeValueForms.html.twig');

        /** @var ProductAttribute[] $attributes */
        $attributes = $this->repository->findBy([
            'code' => $request->query->all('sylius_product_attribute_choice'),
        ]);

        if (empty($attributes)) {
            throw new BadRequestHttpException();
        }

        $localeCodes = $this->get('sylius.translation_locale_provider')->getDefinedLocalesCodes();

        $forms = [];
        foreach ($attributes as $attribute) {
            $forms[$attribute->getCode()] = $this->getAttributeFormsInAllLocales($attribute, $localeCodes);
        }

        return $this->render($template, [
            'forms' => $forms,
            'count' => $request->query->get('count'),
            'metadata' => $this->metadata,
        ]);
    }

    /**
     * @param array|string[] $localeCodes
     *
     * @return array|FormView[]
     */
    protected function getAttributeFormsInAllLocales(AttributeInterface $attribute, array $localeCodes): array
    {
        $attributeForm = $this->get('sylius.form_registry.attribute_type')->get($attribute->getType(), 'default');

        $forms = [];

        if (!$attribute->isTranslatable()) {
            $adminLocaleCode = $this->get(LocaleContextInterface::class)->getLocaleCode();

            return [null => $this->createFormAndView($attributeForm, $attribute, $adminLocaleCode)];
        }

        foreach ($localeCodes as $localeCode) {
            $forms[$localeCode] = $this->createFormAndView($attributeForm, $attribute, $localeCode);
        }

        return $forms;
    }

    private function createFormAndView(
        $attributeForm,
        AttributeInterface $attribute,
        string $localeCode,
    ): FormView {
        return $this
            ->get('form.factory')
            ->createNamed(
                'value',
                $attributeForm,
                null,
                [
                    'label' => $attribute->getTranslation($localeCode)->getName(),
                    'configuration' => $attribute->getConfiguration(),
                    'locale_code' => $localeCode,
                ],
            )
            ->createView()
        ;
    }
}
