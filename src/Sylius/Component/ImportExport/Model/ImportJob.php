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
class ImportJob extends Job implements ImportJobInterface
{
    /**
     * @var ProfileInterface
     */
    private $importProfile;

    /**
     * {@inheritdoc}
     */
    public function getImportProfile()
    {
        return $this->importProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function setImportProfile(ImportProfile $importProfile)
    {
        $this->importProfile = $importProfile;

        return $this;
    }
}