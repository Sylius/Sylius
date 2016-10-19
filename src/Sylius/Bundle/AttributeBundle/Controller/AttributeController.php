<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeController extends ResourceController
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

        $form = $this->get('form.factory')->create(
            sprintf('sylius_%s_choice', $this->metadata->getName()),
            null,
            [
                'expanded' => true,
                'multiple' => true,
            ]
        );

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

        $attributeRepository = $this->get($this->metadata->getServiceId('repository'));
        $forms = [];

        $choices = $request->query->get(sprintf('sylius_%s_choice', $this->metadata->getName()), []);

        $attributes = $attributeRepository->findBy(['id' => $choices]);
        foreach ($attributes as $attribute) {
            $forms[$attribute->getId()] = $this->getAttributeForm($attribute);
        }

        return $this->render($template, [
            'forms' => $forms,
            'count' => $request->query->get('count'),
            'metadata' => $this->metadata,
        ]);
    }

    /**
     * @param AttributeInterface $attribute
     *
     * @return FormView
     */
    private function getAttributeForm(AttributeInterface $attribute)
    {
        $attributeForm = 'sylius_attribute_type_'.$attribute->getType();

        $form = $this
            ->get('form.factory')
            ->createNamed('value', $attributeForm, null, ['label' => $attribute->getName()])
        ;

        return $form->createView();
    }
}
