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

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Bundle\ApiBundle\Uploader\ImageUploaderInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class UploadTaxonImageAction
{
    public function __construct(private ImageUploaderInterface $taxonImageUploader)
    {
    }

    public function __invoke(Request $request): ImageInterface
    {
        if ($request->getMethod() === Request::METHOD_PUT) {
            return $this->taxonImageUploader->modify(
                $request->attributes->get('code', ''),
                $request->attributes->get('id', ''),
                $request->files->get('file'),
                $request->request->get('type'),
            );
        }

        return $this->taxonImageUploader->create(
            $request->attributes->get('code', ''),
            $request->files->get('file'),
            $request->request->get('type'),
        );
    }
}
