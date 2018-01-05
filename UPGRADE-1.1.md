# UPGRADE FROM 1.0 to 1.1

* Scanning for `composer.json` file inside themes directories is recursive by default, which can result in slow performance
  when e.g. a `node_modules` folder is present inside a theme folder. Supply the optional `scan_depth` (integer) setting
  to the `sylius_theme` configuration to restrict scanning for the theme configuration file to a specific depth inside
  the specified theme directories.
  
* Methods `createQueryBuilderByProductCode` and `findOneByIdAndProductCode` were added to
  `Sylius\Component\Core\Repository\ProductReviewRepositoryInterface` (no manual action needed if your implementation
  extends `Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductReviewRepository`).
