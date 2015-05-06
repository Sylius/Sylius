<?php

namespace Sylius\Component\ImportExport\Logger\Factory;

use Sylius\Component\ImportExport\Logger\Model\StreamHandler;
use Sylius\Component\ImportExport\Provider\CurrentDateProviderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface StreamHandlerFactoryInterface
{
    /**
     * Creates new StreamHandler object.
     *
     * @param string $profileName
     *
     * @return StreamHandler
     */
    public function create($profileName);
}
