@receiving_discount
Feature: Receiving discount with independent promotion rules configured per channel
    In order to pay proper amount while buying promoted goods with different rules per channel
    As a Customer
    I want to have promotions with independent rules applied correctly in different channels

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency and with hostname "united.states"
        And the store operates on another channel named "Web-GB" in "GBP" currency and with hostname "great.britain"
        And the store has a product "PHP T-Shirt" priced at "$100.00" in "Web-US" channel
        And this product is also priced at "£80.00" in "Web-GB" channel
        And the store has a product "Symfony T-Shirt" priced at "$60.00" in "Web-US" channel
        And this product is also priced at "£40.00" in "Web-GB" channel
        And the store has a product "Golang T-Shirt" priced at "$50.00" in "Web-US" channel
        And this product is also priced at "£35.00" in "Web-GB" channel
        And there is a promotion "Holiday promotion"
        And I am a logged in customer

    @api @ui
    Scenario: Receiving discount with different total rules per channel
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£5.00" discount to every order in the "Web-GB" channel
        And this promotion only applies to orders with a total of at least "$100.00" for "Web-US" channel and "$50.00" for "Web-GB" channel
        When I changed my current channel to "Web-US"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Receiving discount skips rules not configured for current channel
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£5.00" discount to every order in the "Web-GB" channel
        And this promotion only applies to orders with a total of at least "$100.00" for "Web-US" channel
        When I changed my current channel to "Web-GB"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£75.00"
        And my discount should be "-£5.00"

    @api @ui
    Scenario: Not receiving discount when rule with independent channel configuration is not met
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£5.00" discount to every order in the "Web-GB" channel
        And this promotion only applies to orders with a total of at least "$100.00" for "Web-US" channel and "$150.00" for "Web-GB" channel
        When I changed my current channel to "Web-GB"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£80.00"
        And there should be no discount applied

    @api @ui
    Scenario: Promotion with different rule types per channel - item total for US, cart quantity for GB
        Given the promotion has an item total rule for at least "$100.00" in "Web-US" channel
        And the promotion has a cart quantity rule for at least 2 items in "Web-GB" channel
        And the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£3.00" discount to every order in the "Web-GB" channel
        When I changed my current channel to "Web-US"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Promotion with different rule types per channel - Web-GB ignores item total, requires quantity
        Given the promotion has an item total rule for at least "$100.00" in "Web-US" channel
        And the promotion has a cart quantity rule for at least 2 items in "Web-GB" channel
        And the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£3.00" discount to every order in the "Web-GB" channel
        When I changed my current channel to "Web-GB"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£80.00"
        And there should be no discount applied

    @api @ui
    Scenario: Promotion with different rule types per channel - Web-GB accepts with required quantity
        Given the promotion has an item total rule for at least "$100.00" in "Web-US" channel
        And the promotion has a cart quantity rule for at least 2 items in "Web-GB" channel
        And the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£3.00" discount to every order in the "Web-GB" channel
        When I changed my current channel to "Web-GB"
        And I added product "Golang T-Shirt" to the cart
        And I added product "Golang T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£67.00"
        And my discount should be "-£3.00"

    @api @ui
    Scenario: Single per-channel cart quantity rule accepts the order when the channel threshold is met
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£3.00" discount to every order in the "Web-GB" channel
        And the promotion has a per channel cart quantity rule requiring at least 1 items in "Web-US" channel and 2 items in "Web-GB" channel
        When I changed my current channel to "Web-US"
        And I added product "Golang T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$40.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Single per-channel cart quantity rule rejects the order when the channel threshold is not met
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£3.00" discount to every order in the "Web-GB" channel
        And the promotion has a per channel cart quantity rule requiring at least 1 items in "Web-US" channel and 2 items in "Web-GB" channel
        When I changed my current channel to "Web-GB"
        And I added product "Golang T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£35.00"
        And there should be no discount applied

    @api @ui
    Scenario: Single per-channel cart quantity rule accepts the second channel once its higher threshold is met
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel and "£3.00" discount to every order in the "Web-GB" channel
        And the promotion has a per channel cart quantity rule requiring at least 1 items in "Web-US" channel and 2 items in "Web-GB" channel
        When I changed my current channel to "Web-GB"
        And I added product "Golang T-Shirt" to the cart
        And I added product "Golang T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£67.00"
        And my discount should be "-£3.00"

    @api @ui
    Scenario: Not receiving discount when action is excluded from customer's channel
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel only
        When I changed my current channel to "Web-GB"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£80.00"
        And there should be no discount applied

    @api @ui
    Scenario: Receiving discount when customer's channel matches action configuration
        Given the promotion gives "$10.00" discount to every order in the "Web-US" channel only
        When I changed my current channel to "Web-US"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"
