@checkout
Feature: Order promotions integrity
    In order to have valid promotions applied on my order
    As a Customer
    I want to have information about promotion changes on my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows paying Offline
        And the store ships everywhere for Free
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Christmas sale"
        And I am a logged in customer

    @ui @api
    Scenario: Preventing customer from completing checkout with already expired promotion
        Given this promotion gives "$10.00" discount to every order
        And this promotion expires tomorrow
        And I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Offline" payment method
        And this promotion has already expired
        When I try to confirm my order
        Then I should be informed that this promotion is no longer applied
        And I should not see the thank you page

    @ui @api
    Scenario: Recalculating cart when promotion already expired
        Given this promotion gives "$10.00" discount to every order
        And this promotion expires tomorrow
        And I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Offline" payment method
        And this promotion has already expired
        When I try to confirm my order
        And I confirm my order
        Then I should see the thank you page

    @ui @api
    Scenario: Being able to completing checkout with several promotions
        Given this promotion gives "12%" discount to every order
        And there is a promotion "New Year" with priority 2
        And the promotion gives "$10.00" discount to every order with items total at least "$100.00"
        And I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Offline" payment method
        When I confirm my order
        Then I should see the thank you page

    @ui @api
    Scenario: Receiving percentage discount when buying items for the required total value
        Given the promotion gives "50%" discount to every order with items total at least "$80.00"
        And I added product "PHP T-Shirt" to the cart
        When I proceed selecting "Offline" payment method
        Then my order total should be "$50.00"

    @ui @api
    Scenario: Successfully placing an order with percentage discount when buying items for the required total value
        Given the promotion gives "50%" discount to every order with items total at least "$80.00"
        And I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Offline" payment method
        When I confirm my order
        Then I should see the thank you page

    @ui @api
    Scenario: Excluded tax is not taken into account into promotion integrity check
        Given the store has "VAT" tax rate of 20% for "Clothes" within the "US" zone
        And this product belongs to "Clothes" tax category
        And this promotion gives "50%" discount to every order
        And I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Offline" payment method
        When I confirm my order
        Then I should see the thank you page

    @ui @api
    Scenario: Preventing customer from completing checkout with already archived promotion
        Given this promotion gives "$10.00" discount to every order
        And I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Offline" payment method
        And the promotion "Christmas sale" is archived
        When I try to confirm my order
        Then I should be informed that this promotion is no longer applied
        And I should not see the thank you page
