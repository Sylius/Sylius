@viewing_cart
Feature: Viewing a cart summary
    In order to see details about my order
    As a visitor
    I want to be able to see my cart summary

    Background:
        Given the store operates on a single channel in "France"

    @ui
    Scenario: Viewing information about empty cart
        When I open cart summary page
        Then I should be notified that my cart is empty

    @ui
    Scenario: Viewing cart summary
        Given the store has a product "T-shirt banana" priced at "€12.54"
        And I add this product to the cart
        When I open cart summary page
        Then I should see "Unit price"
        And unit price value should be "€12.54"
        And I should see "Total"
        And total value should be "€12.54"
        And I should see "Qty"
        And quantity value should be 1
        And I should see "Shipping total"
        And shipping total value should be "€0.00"
        And I should see "Tax total"
        And tax total value should be "€0.00"
        And I should see "Grand total"
        And grand total value should be "€12.54"
