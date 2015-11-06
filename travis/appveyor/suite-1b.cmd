call bin\behat --strict -f progress -s users
call bin\behat --strict -f progress -s search

call bin\behat --strict -f progress -s checkout features/frontend/checkout_addressing.feature
call bin\behat --strict -f progress -s checkout features/frontend/checkout_finalize.feature
call bin\behat --strict -f progress -s checkout features/frontend/checkout_inventory.feature
call bin\behat --strict -f progress -s checkout features/frontend/checkout_payment.feature
call bin\behat --strict -f progress -s checkout features/frontend/checkout_security.feature
call bin\behat --strict -f progress -s checkout features/frontend/checkout_shipping.feature
call bin\behat --strict -f progress -s checkout features/frontend/checkout_start.feature
call bin\behat --strict -f progress -s checkout features/frontend/checkout_taxation.feature

call bin\behat --strict -f progress -s checkout features/frontend/cart_taxation.feature
call bin\behat --strict -f progress -s checkout features/frontend/cart_tax_categories.feature
call bin\behat --strict -f progress -s checkout features/frontend/cart_inclusive_tax.feature
