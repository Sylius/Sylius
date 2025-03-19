@viewing_price_history
Feature: Seeing the correct catalog price history after editing a product variant
    In order to be aware of historical product prices
    As an Administrator
    I want to see the catalog price history of the product I've changed

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Wyborowa Vodka" priced at "$40.00" in "United States" channel
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing historical product variant prices after the product variant has been edited
        When I want to modify the "Wyborowa Vodka" product variant
        And I change its price to "$42.00" for "United States" channel
        And I save my changes
        And I go to the price history of a variant with code "WYBOROWA_VODKA"
        Then I should see 2 log entries in the catalog price history
        And there should be a log entry on the 1st position with the "$42.00" selling price, no original price and datetime of the price change
        And there should be a log entry on the 2nd position with the "$40.00" selling price, no original price and datetime of the price change
