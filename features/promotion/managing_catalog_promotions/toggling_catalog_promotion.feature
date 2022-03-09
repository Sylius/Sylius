@managing_catalog_promotions
Feature: Toggling catalog promotion
    In order to manage which catalog promotions are available to customers
    As an Administrator
    I want to be able to enable or disable a catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And there is a catalog promotion "Christmas sale" that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And I am logged in as an administrator

    @api @ui
    Scenario: Disabling catalog promotion
        When I disable "Christmas sale" catalog promotion
        Then this catalog promotion should be inactive
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Disabling catalog promotion during its operating time
        Given the catalog promotion "Christmas sale" operates between "yesterday" and "tomorrow"
        When I disable "Christmas sale" catalog promotion
        Then this catalog promotion should be inactive
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Enabling catalog promotion
        Given this catalog promotion is disabled
        When I enable "Christmas sale" catalog promotion
        Then this catalog promotion should be active
        And "PHP T-Shirt" variant should be discounted

    @api @ui
    Scenario: Enabling catalog promotion during its operating time
        Given the catalog promotion "Christmas sale" operates between "yesterday" and "tomorrow"
        And this catalog promotion is disabled
        When I enable "Christmas sale" catalog promotion
        Then this catalog promotion should be active
        And "PHP T-Shirt" variant should be discounted

    @api @ui
    Scenario: Enabling catalog promotion outside its operating time starts tomorrow
        Given the catalog promotion "Christmas sale" starts at "tomorrow"
        And this catalog promotion is disabled
        When I enable "Christmas sale" catalog promotion
        Then this catalog promotion should still be inactive
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Enabling catalog promotion outside its operating time ended yesterday
        Given the catalog promotion "Christmas sale" ended "yesterday"
        When I enable "Christmas sale" catalog promotion
        Then this catalog promotion should still be inactive
        And "PHP T-Shirt" variant should not be discounted
