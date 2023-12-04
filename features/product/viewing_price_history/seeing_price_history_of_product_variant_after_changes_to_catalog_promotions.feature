@viewing_price_history_after_catalog_promotions
Feature: Seeing the price history of a product variant after changes to catalog promotions
    In order to be aware of historical variant prices
    As an Administrator
    I want to browse the catalog price history of a specific variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And there is a catalog promotion "Winter sale" with priority 1 that reduces price by "50%" and applies on "Wyborowa Vodka" product
        And there is a catalog promotion "Christmas sale" with priority 2 that reduces price by fixed "$5.00" in the "United States" channel and applies on "Wyborowa Vodka" product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Exquisite" variant priced at "$40.00" and originally priced at "$15.00"
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Lemon" variant priced at "$10.00"
        And there is an exclusive catalog promotion "Extra sale" with priority 10 that reduces price by "10%" and applies on "Wyborowa Vodka Lemon" variant
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing the catalog price history of a variant with many catalog promotions
        And I go to the price history of a variant with code "WYBOROWA_VODKA_EXQUISITE"
        Then I should see 2 log entries in the catalog price history
        And there should be a log entry on the 1st position with the "$5.00" selling price, "$15.00" original price and datetime of the price change
        And there should be a log entry on the 2nd position with the "$40.00" selling price, "$15.00" original price and datetime of the price change

    @api @ui
    Scenario: Seeing the catalog price history of a variant with one catalog promotion
        And I go to the price history of a variant with code "WYBOROWA_VODKA_LEMON"
        Then I should see 2 log entries in the catalog price history
        And there should be a log entry on the 1st position with the "$9.00" selling price, "$10.00" original price and datetime of the price change
        And there should be a log entry on the 2nd position with the "$10.00" selling price, no original price and datetime of the price change
