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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
}
