@checkout
Feature: Displaying label of promotion in order language
    In order to the proper information about promotion
    As a Customer
    I want to have the promotion label displayed in the order language

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "Polish (Poland)" locale by default
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And there is a promotion "Holiday promotion"
        And the promotion has label translation "Promocja wakacyjna" in the "Polish (Poland)" locale
        And the promotion has label translation "Holiday promotion" in the "English (United States)" locale
        And the promotion gives "50%" off on every product when the item total is at least "$10.00"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer

    @ui
    Scenario: Displaying label in order language when the promotion is applied
        Given I have product "PHP T-Shirt" in the cart using "English (United States)" locale
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed selecting "Offline" payment method
        Then I should see the promotion label named "Holiday promotion"

    @ui
    Scenario: Switching locale after adding product to cart
        Given I have product "PHP T-Shirt" in the cart using "English (United States)" locale
        And I change my locale to "Polish (Poland)"
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed selecting "Offline" payment method
        Then I should see the promotion label named "Holiday promotion"
