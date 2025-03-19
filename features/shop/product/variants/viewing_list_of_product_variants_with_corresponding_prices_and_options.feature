@viewing_product_variants
Feature: Viewing corresponding prices and options of product variants
    In order to distinguish between product variants
    As a Visitor
    I want to be able to view corresponding prices and options of product variants

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a "Raspberry Pi" configurable product
        And this product has option "Memory" with values "1GB", "2GB" and "4GB"
        And this product with "memory" option "1GB" is priced at "$21.00"
        And this product with "memory" option "2GB" is priced at "$42.00"
        And this product with "memory" option "4GB" is priced at "$84.00"

    @api @no-ui
    Scenario: Viewing product variants with corresponding prices and options
        When I view variants of the "Raspberry Pi" product
        Then I should see variant with "Memory" option and "1GB" option value priced at "$21.00" at 1st position
        And I should see variant with "Memory" option and "2GB" option value priced at "$42.00" at 2nd position
        And I should see variant with "Memory" option and "4GB" option value priced at "$84.00" at 3rd position

    @api @no-ui
    Scenario: Filtering product variants by option
        When I view variants of the "Raspberry Pi" product
        And I filter variants by "2GB" option value
        Then I should see variant with "Memory" option and "2GB" option value priced at "$42.00" at 1st position
        And I should not see variant with "Memory" option "1GB"
        And I should not see variant with "Memory" option "4GB"
