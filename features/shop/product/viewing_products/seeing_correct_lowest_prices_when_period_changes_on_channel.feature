@viewing_products
Feature: Seeing the correct product's lowest price according to the period set on the channel
    In order to know whether the current discount is attractive
    As a Guest
    I want to see a product's lowest price within the channel's set period

    Background:
        Given the store operates on a single channel in "United States"
        And it is "2023-01-01" now
        And the store has a product "Wyborowa Vodka" priced at "$10.00"
        And on "2023-01-05" its price changed to "$9.00"
        And on "2023-02-05" its price changed to "$11.00"
        And on "2023-02-15" its price changed to "$13.00" and original price to "$10.00"
        And on "2023-02-25" its price changed to "$15.00" and original price to "$20.00"
        And it is "2023-03-01" now

    @api @ui
    Scenario: Seeing the correct lowest price from 60 days before the discount
        Given this channel has 60 days set as the lowest price for discounted products checking period
        When I check this product's details
        Then I should see "$9.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the correct lowest price from 30 days before the discount
        Given this channel has 30 days set as the lowest price for discounted products checking period
        When I check this product's details
        Then I should see "$9.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the correct lowest price from 20 days before the discount
        Given this channel has 20 days set as the lowest price for discounted products checking period
        When I check this product's details
        Then I should see "$9.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the correct lowest price from 10 days before the discount
        Given this channel has 10 days set as the lowest price for discounted products checking period
        When I check this product's details
        Then I should see "$11.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the correct lowest price from 5 days before the discount
        Given this channel has 5 days set as the lowest price for discounted products checking period
        When I check this product's details
        Then I should see "$13.00" as its lowest price before the discount

    @api @ui
    Scenario: Seeing the correct lowest price from 1 day before the discount
        Given this channel has 1 day set as the lowest price for discounted products checking period
        When I check this product's details
        Then I should see "$13.00" as its lowest price before the discount
