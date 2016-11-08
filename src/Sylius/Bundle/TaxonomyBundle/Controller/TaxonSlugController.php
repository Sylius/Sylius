<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxonSlugController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function generateAction(Request $request)
    {
        $name = $request->query->get('name');
        $parentId = $request->query->get('parentId');

        return new JsonResponse([
            'slug' => $this->get('sylius.generator.taxon_slug')->generate($name, $parentId),
        ]);
    }
}
