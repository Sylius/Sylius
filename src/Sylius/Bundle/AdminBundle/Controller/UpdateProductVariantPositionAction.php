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

namespace Sylius\Bundle\AdminBundle\Controller;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @experimental
 */
final readonly class UpdateProductVariantPositionAction
{
    private const CSRF_TOKEN_NAME = 'update-product-variant-position';

    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ObjectManager $manager,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $this->validateCsrfProtection(new CsrfToken(self::CSRF_TOKEN_NAME, $data['_csrf_token'] ?? ''));

        foreach ($data['productVariants'] ?? [] as $productVariantToUpdate) {
            if (!is_numeric($productVariantToUpdate['position'])) {
                throw new HttpException(
                    Response::HTTP_NOT_ACCEPTABLE,
                    sprintf('The product variant position "%s" is invalid.', $productVariantToUpdate['position']),
                );
            }

            /** @var ProductVariantInterface|null $productVariant */
            $productVariant = $this->productVariantRepository->find($productVariantToUpdate['id']);
            if (null === $productVariant) {
                throw new NotFoundHttpException(sprintf('The product variant with id "%s" has not been found.', $productVariantToUpdate['id']));
            }

            $productVariant->setPosition((int) $productVariantToUpdate['position']);
            // Flushing in the loop is required, as Gedmo Sortable recalculates the positions of the other variants on each flush
            $this->manager->flush();
        }

        return new JsonResponse();
    }

    private function validateCsrfProtection(CsrfToken $csrfToken): void
    {
        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }
    }
}
