@checkout
Feature: Seeing order addresses on order summary page when shipping is the required one for the channel
    In order to be certain about shipping and billing address
    As a Customer
    I want to be able to see proper addresses on the order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And its required address in the checkout is shipping
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step

    @api @ui
    Scenario: Seeing the same shipping and billing address on order summary
        When I specify the required shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        Then I should be on the checkout summary step
        And address to "Jon Snow" should be used for both shipping and billing of my order

    @api @ui
    Scenario: Seeing different shipping and billing addresses on order summary
        When I specify the required shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I specify different billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Eddard Stark"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        Then I should be on the checkout summary step
        And my order's shipping address should be to "Jon Snow"
        But my order's billing address should be to "Eddard Stark"
