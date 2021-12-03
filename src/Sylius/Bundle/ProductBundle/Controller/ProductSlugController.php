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

use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductSlugController extends AbstractController
{
    private ?SlugGeneratorInterface $slugGenerator;

    public function __construct(?SlugGeneratorInterface $slugGenerator = null)
    {
        $this->slugGenerator = $slugGenerator;

        if ($this->slugGenerator === null) {
            @trigger_error(sprintf('Not passing a $slugGenerator to %s constructor is deprecated since Sylius 1.11 and will be prohibited in Sylius 2.0.', self::class), \E_USER_DEPRECATED);
        }
    }

    /**
     * @psalm-suppress DeprecatedMethod
     */
    public function generateAction(Request $request): Response
    {
        $name = $request->query->get('name');

        if ($this->slugGenerator !== null) {
            return new JsonResponse([
                'slug' => $this->slugGenerator->generate((string) $name),
            ]);
        }

        return new JsonResponse([
            'slug' => $this->get('sylius.generator.slug')->generate($name),
        ]);
    }
}
