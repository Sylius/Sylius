<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeChoiceType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductAttributeController extends ResourceController
{
    /**
     * @param Request $request
     * @param string $template
     *
     * @return Response
     */
    public function getAttributeTypesAction(Request $request, $template)
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

    /**
     * @return Response
     */
    public function renderAttributesAction(Request $request)
    {
        $template = $request->attributes->get('template', 'SyliusAttributeBundle::attributeChoice.html.twig');

        $form = $this->get('form.factory')->create(ProductAttributeChoiceType::class, null, [
            'multiple' => true,
        ]);

        return $this->render($template, ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAttributeValueFormsAction(Request $request)
    {
        $template = $request->attributes->get('template', 'SyliusAttributeBundle::attributeValueForms.html.twig');

        $form = $this->get('form.factory')->create(ProductAttributeChoiceType::class, null, [
            'multiple' => true,
        ]);
        $form->handleRequest($request);

        $attributes = $form->getData();
        if (null === $attributes) {
            throw new BadRequestHttpException();
        }

        $localeCodes = $this->get('sylius.translation_locale_provider')->getDefinedLocalesCodes();

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
     * @param AttributeInterface $attribute
     * @param string[] $localeCodes
     *
     * @return FormView[]
     */
    protected function getAttributeFormsInAllLocales(AttributeInterface $attribute, array $localeCodes)
    {
        $attributeForm = $this->get('sylius.form_registry.attribute_type')->get($attribute->getType(), 'default');

        $forms = [];
        foreach ($localeCodes as $localeCode) {
            $forms[$localeCode] = $this
                ->get('form.factory')
                ->createNamed('value', $attributeForm, null, ['label' => $attribute->getName(), 'configuration' => $attribute->getConfiguration()])
                ->createView()
            ;
        }

        return $forms;
    }
}
