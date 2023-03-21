@managing_price_history
Feature: Deleting old channel pricing entry logs
    In order to keep the price history slim
    As an Administrator
    I want to delete old channel pricing entry logs

    Background:
        Given the store operates on a single channel in "United States"
        And it is "2022-03-14" now
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And on "2022-03-20" its price changed to "$20.00"
        And on "2022-03-29" its original price changed to "$25.00"
        And on "2022-04-14" its price changed to "$15.00" and original price to "$30.00"
        And on "2022-04-20" its original price has been removed
        And it is "2022-04-29" now

    @domain
    Scenario: Deleting price history older than 90 days
        When I delete price history older than 90 days
        Then there should be 5 price history entries for this product

    @domain
    Scenario: Deleting price history older than 30 days
        When I delete price history older than 30 days
        Then there should be 2 price history entries for this product
        And this product should have no entry with original price changed to "$25.00"

    @domain
    Scenario: Deleting price history older than 1 day
        When I delete price history older than 1 day
        Then this product's price history should be empty
