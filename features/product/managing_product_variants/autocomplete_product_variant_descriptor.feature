@managing_product_variants
Feature: Product variants autocomplete
    In order to get hints when looking for named product variants
    As an Administrator
    I want to get product variants according to my specified phrase

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Snake" configurable product
        And this product has "Ouroboros", "Boomslang" and "Bimini" variants
        And I am logged in as an administrator

    @api
    Scenario: Getting a hint when looking for product variants
        When I look for a variant with "i" in descriptor within the "Snake" product
        Then I should see 1 product variant on the list
        And I should see the product variant named "Bimini" on the list

    @api
    Scenario: Getting a hint when looking for product variants
        When I look for a variant with "bo" in descriptor within the "Snake" product
        Then I should see 2 product variants on the list
        And I should see the product variants named "Ouroboros" and "Boomslang" on the list

    @api
    Scenario: Getting a hint when looking for product variants
        Given the product "Snake" also has a "Python" variant with code "TIMOR"
        When I look for a variant with "ti" in descriptor within the "Snake" product
        Then I should see 1 product variant on the list
        And I should see the product variant named "Python" on the list

    @api
    Scenario: Getting a hint when looking for product variants
        Given the product "Snake" also has a nameless variant with code "CERBERUS"
        When I look for a variant with "cer" in descriptor within the "Snake" product
        Then I should see 1 product variant on the list
        And I should see the product variant labeled "Snake (CERBERUS)" on the list
