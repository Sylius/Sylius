@viewing_price_history_after_catalog_promotions
Feature: Seeing the price history of a simple product after changes to catalog promotions
    In order to be aware of historical simple product prices
    As an Administrator
    I want to browse the catalog price history of a simple product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Wyborowa Vodka" priced at "$100.00" in "United States" channel
        And there is disabled catalog promotion named "Winter sale"
        And the catalog promotion "Winter sale" is available in "United States"
        And it applies on "Wyborowa Vodka" product
        And it reduces price by "90%"
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing the catalog price history of a simple product
        Given the "Winter sale" catalog promotion is enabled
        When I disable "Winter sale" catalog promotion
        And I go to the price history of a variant with code "WYBOROWA_VODKA"
        Then I should see 3 log entries in the catalog price history
        And there should be a log entry on the 1st position with the "$100.00" selling price, "$100.00" original price and datetime of the price change
        And there should be a log entry on the 2nd position with the "$10.00" selling price, "$100.00" original price and datetime of the price change
        And there should be a log entry on the 3rd position with the "$100.00" selling price, no original price and datetime of the price change

    @api @ui
    Scenario: Seeing the catalog price history of a simple product with a price different than an original price
        Given "Wyborowa Vodka" variant is originally priced at "$120.00" in "United States" channel
        And the "Winter sale" catalog promotion is enabled
        And the "Wyborowa Vodka" product is now priced at "$120.00" and originally priced at "$140.00"
        When I disable "Winter sale" catalog promotion
        And I go to the price history of a variant with code "WYBOROWA_VODKA"
        Then I should see 6 log entries in the catalog price history
        And there should be a log entry on the 1st position with the "$140.00" selling price, "$140.00" original price and datetime of the price change
        And there should be a log entry on the 2nd position with the "$14.00" selling price, "$140.00" original price and datetime of the price change
        And there should be a log entry on the 3rd position with the "$120.00" selling price, "$140.00" original price and datetime of the price change
        And there should be a log entry on the 4th position with the "$12.00" selling price, "$120.00" original price and datetime of the price change
        And there should be a log entry on the 5th position with the "$100.00" selling price, "$120.00" original price and datetime of the price change
        And there should be a log entry on the 6th position with the "$100.00" selling price, no original price and datetime of the price change
