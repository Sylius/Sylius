@ui-checkout
Feature: Checkout with PayPal Express Checkout
    In order to buy products
    As a Customer
    I want to be able to pay with PayPal Express Checkout

    Background:
        Given that store is operating on the France channel
          And default currency is "EUR"
          And there is user "john@example.com" identified by "password123"
          And catalog has a product "PHP T-Shirt" priced at $19.99
          And store allows paying "PayPal Express Checkout"
          And store has free shipping method
          And I am logged in as "john@example.com"

@javascript
    Scenario: Being redirected to the PayPal Express Checkout page
        Given I added product "PHP T-Shirt" to the cart
         When I proceed selecting "PayPal Express Checkout" payment method
          And I confirm my order
         Then I should be redirected to PayPal Express Checkout page

  @javascript
    Scenario: Successful payment
        Given I added product "PHP T-Shirt" to the cart
          And I proceed selecting "PayPal Express Checkout" payment method
          And I confirm my order
         When I sign in to PayPal and pay successfully
         Then I should be redirected back to the thank you page