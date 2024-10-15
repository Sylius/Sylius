@viewing_products
Feature: Seeing the product's lowest price before the discount
    In order to know whether the current discount is attractive
    As a Guest
    I want to see a product's lowest price before the discount

    Background:
        Given the store operates on a single channel in "United States"

    @api @ui
    Scenario: Not seeing the lowest price information on a product with no discount
        Given the store has a product "Wyborowa Vodka" priced at "$21.00"
        When I check this product's details
        Then I should not see information about its lowest price

    @api @ui
    Scenario: Seeing the lowest price information on a product that has a single discount
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$21.00" and original price changed to "$37.00"
        When I check this product's details
        Then I should see "$37.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product that has a discount, and then seeing the discount change
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$21.00" and original price changed to "$37.00"
        And this product's price changed to "$30.00"
        When I check this product's details
        Then I should see "$21.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product that has a discount, over a month passes, and then seeing the discount change
        Given it is "2023-03-14" now
        And the store has a product "Wyborowa Vodka" priced at "$42.00"
        And this product's price changed to "$21.00" and original price changed to "$42.00"
        And it is "2023-04-15" now
        And this product's price changed to "$33.00"
        When I check this product's details
        Then I should see "$21.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product that has a discount and the one of the previous promotions ended over 30 days ago
        Given it is "2022-12-01" now
        And the store has a product "Wyborowa Vodka" priced at "$42.00"
        And this product's price changed to "$21.00" and original price changed to "$42.00"
        And it is "2023-01-01" now
        And this product's price changed to "$39.00"
        And it is "2023-02-15" now
        And this product's price changed to "$33.00"
        When I check this product's details
        Then I should see "$39.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product that has a discount and the previous promotion ended over 30 days ago
        Given it is "2022-12-01" now
        And the store has a product "Wyborowa Vodka" priced at "$42.00"
        And this product's price changed to "$21.00" and original price changed to "$42.00"
        And it is "2023-01-01" now
        And this product's price changed to "$42.00" and original price was removed
        And it is "2023-02-15" now
        And this product's price changed to "$33.00" and original price changed to "$42.00"
        When I check this product's details
        Then I should see "$42.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product that has the lowest price set beyond the period but it is still the lowest price in the period
        Given it is "2023-01-01" now
        And the store has a product "Wyborowa Vodka" priced at "$10.00"
        And on "2023-01-05" its price changed to "$9.00"
        And on "2023-02-05" its price changed to "$11.00"
        And on "2023-02-15" its price changed to "$13.00" and original price to "$10.00"
        And on "2023-02-25" its price changed to "$15.00" and original price to "$20.00"
        And it is "2023-03-01" now
        When I check this product's details
        Then I should see "$9.00" as its lowest price before the discount

    @api @ui
    Scenario: Not seeing the lowest price information on a product that had a discount and the discount was removed
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$21.00" and original price changed to "$37.00"
        And this product's price changed to "$37.00" and original price was removed
        When I check this product's details
        Then I should not see information about its lowest price

    @api @ui
    Scenario: Seeing the lowest price information on a product that had a discount, the discount was removed, the price changed below the discount price, the price changed back to the start value and then the less attractive discount was added
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$21.00" and original price changed to "$37.00"
        And this product's price changed to "$37.00" and original price was removed
        And this product's price changed to "$10.00"
        And this product's price changed to "$21.00"
        And this product's price changed to "$20.00" and original price changed to "$21.00"
        When I check this product's details
        Then I should see "$10.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product that had a discount, the price was changed below previous discount while a less attractive discount was added and a less attractive discount was added
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$20.00" and original price changed to "$37.00"
        And this product's price changed to "$21.00" and original price changed to "$19.00"
        And this product's price changed to "$33.00" and original price changed to "$37.00"
        When I check this product's details
        Then I should see "$20.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product with same discount repeated
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$20.00" and original price changed to "$37.00"
        And this product's price changed to "$37.00" and original price was removed
        And this product's price changed to "$20.00" and original price changed to "$37.00"
        When I check this product's details
        Then I should see "$20.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product with catalog promotion
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And there is a catalog promotion "Winter Sale" with priority 100 that reduces price by "$10.00" and applies on "Wyborowa Vodka" product
        When I check this product's details
        Then I should see "$37.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the lowest price information on a product with catalog promotion and reduced price
        Given the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$20.00"
        And there is a catalog promotion "Winter Sale" with priority 100 that reduces price by "$10.00" and applies on "Wyborowa Vodka" product
        When I check this product's details
        Then I should see "$20.00" as its lowest price before the discount

    @api @ui
    Scenario: Not seeing the lowest price information on a product that has a discount but showing it is disabled on the channel
        Given the lowest price of discounted products prior to the current discount is disabled on this channel
        And the store has a product "Wyborowa Vodka" priced at "$37.00"
        And this product's price changed to "$21.00" and original price changed to "$37.00"
        And this product's price changed to "$30.00"
        When I check this product's details
        Then I should not see information about its lowest price
