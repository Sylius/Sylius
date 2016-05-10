@viewing_cart
Feature: Removing cart item from cart
    In order to delete some unnecessary cart items
    As a visitor
    I want to be able to remove cart item

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "T-shirt banana" priced at "€12.54"
        And I add this product to the cart

    @ui
    Scenario: Removing cart item
        Given I open cart summary page
        When I delete this product
        Then I should be notified that this product has been successfully deleted

    @ui
    Scenario: Removing cart item from cart with multiple items
        Given the store has a product "T-shirt potato" priced at "€18.30"
        And I add this product to the cart
        And I open cart summary page
        When I delete this product
        Then I should be notified that this product has been successfully deleted
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