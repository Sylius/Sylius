/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

export default [
  'sylius_product',
  'sylius_taxon',
  'sylius_product_variant',
  'sylius_product_generate_variants',
  'sylius_inventory',
  'sylius_product_attribute',
  'sylius_product_option',
  'sylius_product_association_type',
  'sylius_customer',
  'sylius_customer_group',
  'sylius_promotion',
  'sylius_promotion_coupon',
  'sylius_promotion_coupon_generator_instruction',
  'sylius_product_review',
  'sylius_channel',
  'sylius_country',
  'sylius_zone',
  'sylius_currency',
  'sylius_exchange_rate',
  'sylius_locale',
  'sylius_payment_method',
  'sylius_shipping_method',
  'sylius_shipping_category',
  'sylius_tax_category',
  'sylius_tax_rate',
  'sylius_admin_user',
]
  .map(form => `form[name="${form}"]`)
  .join(', ');
