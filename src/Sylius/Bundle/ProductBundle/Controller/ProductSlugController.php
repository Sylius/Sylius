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

use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductSlugController extends AbstractController
{
    public function __construct(private ?SlugGeneratorInterface $slugGenerator = null)
    {
        if ($this->slugGenerator === null) {
            trigger_deprecation(
                'sylius/product-bundle',
                '1.11',
                'Not passing a $slugGenerator to %s constructor is deprecated and will be prohibited in Sylius 2.0.',
                self::class,
            );
        }
    }

    public function generateAction(Request $request): Response
    {
        $name = $request->query->get('name');

        if ($this->slugGenerator !== null) {
            return new JsonResponse([
                'slug' => $this->slugGenerator->generate((string) $name),
            ]);
        }

        return new JsonResponse([
            'slug' => $this->container->get('sylius.generator.slug')->generate($name),
        ]);
    }
}
