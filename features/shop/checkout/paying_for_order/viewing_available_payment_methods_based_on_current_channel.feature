@paying_for_order
Feature: Viewing available payment methods based on current channel
    In order to be able to pay for order in preferable payment method
    As a Customer
    I want to see all available for me payment methods

    Background:
        Given the store operates on another channel named "United States" in "USD" currency and with hostname "usa.cool-clothes.example"
        And the store operates on a channel named "Poland" with hostname "polski-sklep.pl"
        And the store has a zone "World"
        And the store ships to "United States"
        And this zone has the "United States" country member
        And the store allows paying with "Bank of America" in "United States" channel
        And the store allows paying with "Bank of Poland" in "Poland" channel
        And the store allows paying Offline for all channels
        And the store allows paying "Bank of Universe"
        And this payment method has been disabled
        And the store has a product "PHP T-Shirt" priced at "$19.99" in "United States" channel
        And this product is also priced at "$25.00" in "Poland" channel
        And the store ships everywhere for free for all channels

    @ui @api
    Scenario: Seeing payment methods that are available in channel as a logged in customer
        Given I am a logged in customer
        And I am in the "United States" channel
        And I have product "PHP T-Shirt" in the cart
        When I complete addressing step with "United States" based billing address
        And I complete the shipping step with first shipping method
        Then I should be on the checkout payment step
        And I should see "Bank of America" and "Offline" payment methods
        But I should not see "Bank of Poland" and "Bank of Universe" payment methods

    @api @ui
    Scenario: Seeing shipping methods that are available in another channel as an logged in customer
        Given I am a logged in customer
        And I am in the "Poland" channel
        And I have product "PHP T-Shirt" in the cart
        When I complete addressing step with "United States" based billing address
        And I complete the shipping step with first shipping method
        Then I should be on the checkout payment step
        And I should see "Bank of Poland" and "Offline" payment methods
        But I should not see "Bank of Universe" and "Bank of America" payment methods

    @ui @api
    Scenario: Seeing payment methods that are available in channel as a guest
        Given I am in the "United States" channel
        And I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I complete the shipping step with first shipping method
        Then I should be on the checkout payment step
        And I should see "Bank of America" and "Offline" payment methods
        But I should not see "Bank of Poland" and "Bank of Universe" payment methods

    @ui @api
    Scenario: Seeing shipping methods that are available in another channel as a guest
        Given I am in the "Poland" channel
        And I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I complete the shipping step with first shipping method
        Then I should be on the checkout payment step
        And I should see "Bank of Poland" and "Offline" payment methods
        But I should not see "Bank of Universe" and "Bank of America" payment methods
