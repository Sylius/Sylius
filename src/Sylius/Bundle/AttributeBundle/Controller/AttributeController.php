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

use Sylius\Bundle\AttributeBundle\AttributeType\TextAttributeType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeController extends ResourceController
{
    /**
     * @return AttributeInterface
     */
    public function createNew()
    {
        $attribute = parent::createNew();

        $type = $this->getNewAttributeType();
        $attribute->setType($type);
        $attributeType = $this->get('sylius.registry.attribute_type')->get($type);
        $attribute->setStorageType($attributeType->getStorageType());

        return $attribute;
    }

    /**
     * @return Response
     */
    public function renderAttributeTypesAction()
    {
        $attributeTypes = $this->get('sylius.registry.attribute_type')->all();

        return $this->render('SyliusWebBundle:Backend/ProductAttribute:attributeTypesModal.html.twig', array('attributeTypes' => $attributeTypes));
    }

    /**
     * @return Response
     */
    public function renderAttributesAction()
    {
        $form = $this->get('form.factory')->create(
            'sylius_product_attribute_choice',
            null,
            array(
                'expanded' => true,
                'multiple' => true,
            )
        );

        return $this->render('SyliusWebBundle:Backend/ProductAttribute:attributeChoice.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAttributeTypesFormsAction(Request $request)
    {
        $attributeRepository = $this->get('sylius.repository.product_attribute');
        $forms = array();

        foreach ($request->query->get('sylius_product_attribute_choice') as $choice) {
            /** @var AttributeInterface $attribute */
            $attribute = $attributeRepository->find($choice);
            $attributeForm = 'sylius_attribute_type_'.$attribute->getType();

            $options = array('label' => $attribute->getName());

            $form = $this->get('form.factory')->createNamed('value', $attributeForm, null, $options);
            $forms[$attribute->getId()] = $form->createView();
        }

        return $this->render('SyliusWebBundle:Backend/ProductAttribute:attributeValueForms.html.twig', array('forms' => $forms, 'count' => $request->query->get('count')));
    }

    /**
     * @return string
     */
    private function getNewAttributeType()
    {
        if ($this->getRequest()->query->has('type')) {
            return $this->getRequest()->query->get('type');
        }

        return TextAttributeType::TYPE;
    }
}
