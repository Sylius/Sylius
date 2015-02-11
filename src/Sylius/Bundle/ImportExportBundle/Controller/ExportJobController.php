<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExportJobController extends ResourceController
{
    /**
     * Download exported file
     *
     * @param Request $request
     * @param string $fileName
     *
     * @return Response
     */
    public function downloadAction(Request $request, $id, $fileName)
    {
        $filesystem = $this->container->get('exporter_filesystem');
        $fileInfo = new \SplFileInfo($fileName);

        return new Response(
            $filesystem->read($fileName),
            200,
            array(
                'Content-Type'        => $filesystem->getAdapter()->mimeType($fileName),
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.' . $fileInfo->getExtension()
            )
        );
    }
}
