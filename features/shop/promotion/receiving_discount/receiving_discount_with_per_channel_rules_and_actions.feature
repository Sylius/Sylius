@receiving_discount
Feature: Receiving discount with promotion rules and actions configured independently per channel
    In order to pay the proper amount while buying promoted goods with different rules and actions per channel
    As a Customer
    I want to have per-channel promotion rules and actions applied correctly in my current channel

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency and with hostname "united.states"
        And the store operates on another channel named "Web-GB" in "GBP" currency and with hostname "great.britain"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00" in "Web-US" channel
        And this product is also priced at "£80.00" in "Web-GB" channel
        And the product "PHP T-Shirt" belongs to taxon "T-Shirts"
        And the store has a product "Symfony T-Shirt" priced at "$60.00" in "Web-US" channel
        And this product is also priced at "£40.00" in "Web-GB" channel
        And there is a promotion "Holiday promotion"
        And I am a logged in customer

    @api @ui
    Scenario: Receiving an order percentage discount configured independently per channel
        Given the promotion gives "10%" discount to every order in the "Web-US" channel and "5%" discount to every order in the "Web-GB" channel
        When I changed my current channel to "Web-US"
        And I added product "PHP T-Shirt" to the cart
        And I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Order percentage discount applies the value configured for the current channel
        Given the promotion gives "10%" discount to every order in the "Web-US" channel and "5%" discount to every order in the "Web-GB" channel
        When I changed my current channel to "Web-GB"
        And I added product "PHP T-Shirt" to the cart
        And I check the details of my cart
        Then my cart total should be "£76.00"
        And my discount should be "-£4.00"

    @api @ui
    Scenario: Receiving a discount when the cart contains the product configured for the current channel
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£5.00" discount to every order in the "Web-GB" channel
        And the promotion applies to orders containing product "PHP T-Shirt" in the "Web-US" channel and product "Symfony T-Shirt" in the "Web-GB" channel
        When I changed my current channel to "Web-US"
        And I added product "PHP T-Shirt" to the cart
        And I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Receiving a discount when the cart has a product from the taxon configured for the current channel
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£5.00" discount to every order in the "Web-GB" channel
        And the promotion applies to orders with a product from taxon "T-Shirts" in the "Web-US" channel and from taxon "Mugs" in the "Web-GB" channel
        When I changed my current channel to "Web-US"
        And I added product "PHP T-Shirt" to the cart
        And I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"
