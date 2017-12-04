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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductSlugController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function generateAction(Request $request): Response
    {
        $name = $request->query->get('name');

        return new JsonResponse([
            'slug' => $this->get('sylius.generator.slug')->generate($name),
        ]);
    }
}
