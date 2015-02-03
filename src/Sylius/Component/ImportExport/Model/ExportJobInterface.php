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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ExportJobInterface extends JobInterface
{
    
    /**
     * Gets the value of exportProfile.
     *
     * @return ExportProfile
     */
    public function getExportProfile();

    /**
     * Sets the value of exportProfile.
     *
     * @param ExportProfile $exportProfile the export profile
     *
     * @return self
     */
    public function setExportProfile(ExportProfile $exportProfile);
}