@paying_for_order
Feature: Inform customer about any order total changes during checkout process
    In order inform the customer about any changes that affect order total
    As a Customer
    I want to be able prevent placing order with invalid order total

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "NA VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And it belongs to "Clothes" tax category
        And the store ships everywhere for free
        And the store allows paying offline

    @ui
    Scenario: Inform customer about order total change due to product price change
        Given I am a logged in customer
        And I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        And this product price has been changed to "$25.00"
        When I confirm my order
        Then I should be informed that order total has been changed
        And I should not see the thank you page

    @ui
    Scenario: Be able to confirm order after information appears
        Given I am a logged in customer
        And I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        And this product price has been changed to "$25.00"
        And I have confirmed order
        When I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Inform customer about order total change due to tax change
        Given I am a logged in customer
        And I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        And the "NA VAT" tax rate has changed to 10%
        When I confirm my order
        Then I should be informed that order total has been changed
        And I should not see the thank you page

    @ui
    Scenario: Inform customer about order total change due to shipping method fee change
        Given the store has "UPS" shipping method with "$20.00" fee
        And I added product "PHP T-Shirt" to the cart
        And I have completed addressing step with email "guest@example.com" and "United States" based shipping address
        And I have proceeded order with "UPS" shipping method and "Offline" payment
        And the shipping fee for "UPS" shipping method has been changed to "$30.00"
        When I confirm my order
        Then I should be informed that order total has been changed
        And I should not see the thank you page
