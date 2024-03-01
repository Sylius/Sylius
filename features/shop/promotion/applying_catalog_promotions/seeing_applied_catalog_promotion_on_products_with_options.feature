@applying_catalog_promotions
Feature: Seeing applied catalog promotions on products configured with the option selection method
    In order to be informed about applied catalog promotion on products with given options
    As a Customer
    I want to see a discounted products with given options

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has option "Size" with values "S", "M" and "L"
        And this product has "T-Shirt-Variant-1" variant priced at "$20.00" configured with "S" option value
        And this product has "T-Shirt-Variant-2" variant priced at "$25.00" configured with "M" option value
        And this product has "T-Shirt-Variant-3" variant priced at "$50.00" configured with "L" option value
        And this product is configured with the option matching selection method
        And there is a catalog promotion "Winter sale" that reduces price by "50%" and applies on "T-Shirt-Variant-1" variant
        And there is another catalog promotion "Surprise sale" that reduces price by "25%" and applies on "T-Shirt-Variant-3" variant

    @ui @no-api @javascript
    Scenario: Seeing applied catalog promotion on the product with default option
        When I view product "T-Shirt"
        Then I should see this product is discounted from "$20.00" to "$10.00" with "Winter sale" promotion

    @ui @no-api @javascript
    Scenario: Seeing applied catalog promotion on the product with non default option
        When I view product "T-Shirt"
        And I select its "Size" as "L"
        Then I should see this product is discounted from "$50.00" to "$37.50" with "Surprise sale" promotion

    @ui @no-api @javascript
    Scenario: Seeing applied catalog promotion on the product after changing the options multiple times
        When I view product "T-Shirt"
        And I select its "Size" as "L"
        And I select its "Size" as "S"
        Then I should see this product is discounted from "$20.00" to "$10.00" with "Winter sale" promotion

    @ui @no-api @javascript
    Scenario: Seeing no applied catalog promotion on the product option without applied catalog promotion
        When I view product "T-Shirt"
        And I select its "Size" as "M"
        Then I should see this product is not discounted
