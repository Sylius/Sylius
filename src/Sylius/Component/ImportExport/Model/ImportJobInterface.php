<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Model;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ImportJobInterface extends JobInterface
{
    
    /**
     * Gets the value of importProfile.
     *
     * @return importProfile
     */
    public function getImportProfile();

    /**
     * Sets the value of importProfile.
     *
     * @param ImportProfile $importProfile the import profile
     *
     * @return self
     */
    public function setImportProfile(ImportProfile $importProfile);
}