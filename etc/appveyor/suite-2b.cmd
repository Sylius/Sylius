call bin\behat --strict -f progress -s products features/backend/products.feature
call bin\behat --strict -f progress -s products features/backend/product_attributes.feature
call bin\behat --strict -f progress -s products features/backend/product_options.feature
call bin\behat --strict -f progress -s products features/backend/product_archetypes.feature
call bin\behat --strict -f progress -s products features/backend/product_taxons.feature
call bin\behat --strict -f progress -s products features/backend/product_variants.feature
call bin\behat --strict -f progress -s products features/backend/products_filter.feature
call bin\behat --strict -f progress -s products features/frontend/products.feature

call bin\behat --strict -f progress -s promotions features/backend/promotions.feature
call bin\behat --strict -f progress -s promotions features/frontend/cart_promotions_complex.feature
call bin\behat --strict -f progress -s promotions features/frontend/cart_promotions_coupons.feature
call bin\behat --strict -f progress -s promotions features/frontend/cart_promotions_dates.feature
call bin\behat --strict -f progress -s promotions features/frontend/cart_promotions_fixed.feature
call bin\behat --strict -f progress -s promotions features/frontend/cart_promotions_percentage.feature
call bin\behat --strict -f progress -s promotions features/frontend/cart_promotions_product.feature
call bin\behat --strict -f progress -s promotions features/frontend/cart_promotions_usage_limit.feature
