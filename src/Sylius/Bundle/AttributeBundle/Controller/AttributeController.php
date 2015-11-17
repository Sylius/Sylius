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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Attribute\AttributeType\DefaultAttributeTypes;
use Sylius\Component\Attribute\Model\AttributeInterface;
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

        return $this->render('SyliusWebBundle:Backend/ProductAttribute:attributeTypes.html.twig', array('attributeTypes' => $attributeTypes));
    }

    /**
     * @return string
     */
    private function getNewAttributeType()
    {
        if ($this->getRequest()->query->has('type')) {
            return $this->getRequest()->query->get('type');
        }

        return DefaultAttributeTypes::TEXT;
    }
}
