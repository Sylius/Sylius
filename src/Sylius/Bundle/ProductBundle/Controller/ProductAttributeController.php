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

namespace Sylius\Bundle\ProductBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeChoiceType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

class ProductAttributeController extends ResourceController
{
    public function getAttributeTypesAction(Request $request, string $template): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $view = View::create()
            ->setTemplate($template)
            ->setTemplateVar($this->metadata->getPluralName())
            ->setData([
                'types' => $this->get('sylius.registry.attribute_type')->all(),
                'metadata' => $this->metadata,
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
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

        $form = $this->get('form.factory')->create(ProductAttributeChoiceType::class, null, [
            'multiple' => true,
        ]);
        $form->handleRequest($request);

        $attributes = $form->getData();
        if (null === $attributes) {
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

    public function getAttributesJsonAction(Request $request): JsonResponse
    {
        /** @var string|null $productCode */
        $productCode = $request->attributes->get('productCode');
        Assert::notNull($productCode);

        /** @var ProductRepositoryInterface|null $productRepository */
        $productRepository = $this->get('sylius.repository.product');
        Assert::notNull($productRepository);

        /** @var ProductInterface $product */
        $product = $productRepository->findOneByCode($productCode);

        $attributes = [];

        /** @var AttributeValueInterface $attributeValue */
        foreach ($product->getAttributes() as $attributeValue) {
            /** @var string|null $localeCode */
            $localeCode = $attributeValue->getLocaleCode();

            /** @var ProductAttributeInterface $attribute */
            $attribute = $attributeValue->getAttribute();

            $value = [
                'id' => (string) $attributeValue->getId(),
                'value' => $attributeValue->getValue(),
                'label' => $attribute->getNameByLocaleCode($localeCode)
            ];

            if ($attribute->isTranslatable()) {
                $value['localeCode'] = $localeCode;
            }

            $attributeCode = $attribute->getCode();

            $values = isset($attributes[$attributeCode]['values']) ? $attributes[$attributeCode]['values'] : [];
            $values[] = $value;

            $attributes[$attributeCode] = [
                'code' => $attributeCode,
                'type' => $attributeValue->getType(),
                'translatable' => $attribute->isTranslatable(),
                'values' => $values
            ];
        }

        return new JsonResponse(['attributes' => $attributes]);
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
            array_push($localeCodes, null);

            return $this->createFormAndView($attributeForm, $attribute, null, $forms);
        }

        foreach ($localeCodes as $localeCode) {
            $forms = $this->createFormAndView($attributeForm, $attribute, $localeCode, $forms);
        }

        return $forms;
    }

    private function createFormAndView(
        $attributeForm,
        AttributeInterface $attribute,
        ?string $localeCode,
        array $forms
    ): array {
        $forms[$localeCode] = $this
            ->get('form.factory')
            ->createNamed(
                'value',
                $attributeForm,
                null,
                ['label' => $attribute->getName(), 'configuration' => $attribute->getConfiguration()]
            )
            ->createView();

        return $forms;
    }
}
