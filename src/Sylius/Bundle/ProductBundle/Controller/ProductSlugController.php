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
use Webmozart\Assert\Assert;

class ProductSlugController extends AbstractController
{
    public function __construct(private readonly SlugGeneratorInterface $slugGenerator)
    {
    }

    public function generateAction(Request $request): Response
    {
        $name = $request->query->get('name');
        Assert::string($name);

        return new JsonResponse(['slug' => $this->slugGenerator->generate($name)]);
    }
}
