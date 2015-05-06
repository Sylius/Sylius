<?php

namespace Sylius\Component\ImportExport\Provider;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CurrentDateProviderInterface
{
    /**
     * Provides current date based on given timezone
     *
     * @return \DateTime
     */
    public function getCurrentDate();
}
