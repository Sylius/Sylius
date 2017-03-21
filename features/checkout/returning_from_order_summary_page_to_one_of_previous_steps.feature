@checkout
Feature: Returning from order summary page to one of previous steps
    In order to modify my order after addressing, selecting shipping method and payment method
    As a Customer
    I want to be able to go back to addressing, shipping and payment step from order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Stark Robe" priced at "$56.99"
        And the store has a product "Paganini T-Shirt" priced at "$10.00"
        And there is a promotion "Holiday promotion"
        And the promotion gives "$66.99" discount to every order with quantity at least 2
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"

    @ui
    Scenario: Going back to payment step
        Given I have product "Stark Robe" in the cart
        And I complete addressing step with email "jon@snow.wall" and "United States" based shipping address
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        When I decide to change the payment method
        Then I should be redirected to the payment step
        And I should be able to go to the summary page again

    @ui
    Scenario: Going back to shipping step with steps panel
        Given I have product "Stark Robe" in the cart
        And I complete addressing step with email "jon@snow.wall" and "United States" based shipping address
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        When I go to the shipping step
        Then I should be redirected to the shipping step
        And I should be able to go to the payment step again

    @ui
    Scenario: Going back to addressing step with steps panel
        Given I have product "Stark Robe" in the cart
        And I complete addressing step with email "jon@snow.wall" and "United States" based shipping address
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        When I go to the addressing step
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again

    @ui
    Scenario: Going back to addressing step with steps panel when used an email of customer which had placed an order before
        Given the store has customer "jon@snow.wall"
        And this customer has a "United States" based address in their address book
        And I have product "Stark Robe" in the cart
        And I complete addressing step with email "jon@snow.wall" and "United States" based shipping address
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        When I go to the addressing step
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again

    @ui
    Scenario: Going back to shipping step with steps panel when order total is zero
        Given I have product "Stark Robe" in the cart
        And I have product "Paganini T-shirt" in the cart
        And I complete addressing step with email "jon@snow.wall" and "United States" based shipping address
        And I proceed with "Free" shipping method
        When I go to the shipping step
        Then I should be redirected to the shipping step
        And I should be able to go to the complete step again

    @ui
    Scenario: Going back to addressing step with steps panel when order total is zero
        Given I have product "Stark Robe" in the cart
        And I have product "Paganini T-shirt" in the cart
        And I complete addressing step with email "jon@snow.wall" and "United States" based shipping address
        And I proceed with "Free" shipping method
        When I go to the addressing step
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again
