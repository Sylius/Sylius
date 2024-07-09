@managing_payment_methods
Feature: Payment method validation
    In order to avoid making mistakes when managing a payment method
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "Offline" with a code "Offline"
        And I am logged in as an administrator

    @api @ui
    Scenario: Trying to add a new payment method without specifying its code
        When I want to create a new payment method with "Paypal Express Checkout" gateway factory
        And I name it "Paypal Express Checkout" in "English (United States)"
        But I do not specify its code
        And I add it
        Then I should be notified that code is required
        And the payment method with name "Paypal Express Checkout" should not be added

    @api @no-ui
    Scenario: Trying to add a new payment method translation in unexisting language
        When I want to modify the "Offline" payment method
        And I name it "Offline" in "French (France)"
        And I try to save my changes
        Then I should be notified that the locale is not available

    @api @ui
    Scenario: Trying to add a new payment method with a too long code
        When I want to create a new payment method with "Paypal Express Checkout" gateway factory
        And I name it "Paypal Express Checkout" in "English (United States)"
        And I specify a too long code
        And I add it
        Then I should be notified that code is too long

    @api @ui
    Scenario: Trying to add a new payment method without specifying its name
        When I want to create a new payment method with "Paypal Express Checkout" gateway factory
        And I specify its code as "PEC"
        But I do not name it
        And I add it
        Then I should be notified that I have to specify payment method name
        And the payment method with code "PEC" should not be added

    @api @ui
    Scenario: Trying to add a new paypal payment method without specifying password
        When I want to create a new payment method with "Paypal Express Checkout" gateway factory
        And I name it "Paypal Express Checkout" in "English (United States)"
        And I specify its code as "PEC"
        And I configure it for username "TEST" with "TEST" signature
        But I do not specify configuration password
        And I add it
        Then I should be notified that I have to specify paypal password
        And the payment method with code "PEC" should not be added

    @api @no-ui
    Scenario: Trying to add a new paypal payment method without specifying sandbox
        When I want to create a new payment method with "Paypal Express Checkout" gateway factory
        And I name it "Paypal Express Checkout" in "English (United States)"
        And I specify its code as "PEC"
        And I configure it for username "TEST" with "TEST" signature and password, but without sandbox
        And I add it
        Then I should be notified that I have to specify paypal sandbox status
        And the payment method with code "PEC" should not be added

    @api @no-ui
    Scenario: Trying to add a new paypal payment method, but with sandbox that has wrong type
        When I want to create a new payment method with "Paypal Express Checkout" gateway factory
        And I name it "Paypal Express Checkout" in "English (United States)"
        And I specify its code as "PEC"
        And I configure it for username "TEST" with "TEST" signature and password, but with sandbox that has wrong type
        And I add it
        Then I should be notified that I have to specify paypal sandbox status that is boolean
        And the payment method with code "PEC" should not be added

    @api @ui
    Scenario: Trying to add a new stripe payment method with only publishable key specified
        When I want to create a new payment method with "Stripe Checkout" gateway factory
        And I name it "Stripe Checkout" in "English (United States)"
        And I specify its code as "SC"
        And I configure it with only "Publishable key"
        And I add it
        Then I should be notified that I have to specify stripe "Secret key"
        And the payment method with code "PEC" should not be added

    @api @ui
    Scenario: Trying to add a new stripe payment method with only secret key specified
        When I want to create a new payment method with "Stripe Checkout" gateway factory
        And I name it "Stripe Checkout" in "English (United States)"
        And I specify its code as "SC"
        And I configure it with only "Secret key"
        And I add it
        Then I should be notified that I have to specify stripe "Publishable key"
        And the payment method with code "PEC" should not be added

    @api @no-ui
    Scenario: Trying to add a new payment method without gateway configuration
        When I want to create a new payment method without gateway configuration
        And I name it "Payment method without gateway configuration" in "English (United States)"
        And I specify its code as "PMWGC"
        And I try to add it
        Then I should be notified that I have to specify gateway configuration

    @api @no-ui
    Scenario: Trying to add a new payment method without gateway name
        When I want to create a new payment method without gateway name
        And I name it "Payment method without gateway name" in "English (United States)"
        And I specify its code as "PMWGN"
        And I try to add it
        Then I should be notified that I have to specify gateway name

    @api @no-ui
    Scenario: Trying to add a new payment method without factory name
        When I want to create a new payment method without factory name
        And I name it "Payment method without gateway factory name" in "English (United States)"
        And I specify its code as "PMWFN"
        And I try to add it
        Then I should be notified that I have to specify factory name

    @api @no-ui
    Scenario: Trying to add a new payment method with wrong factory name
        When I want to create a new payment method with wrong factory name
        And I name it "Payment method with wrong gateway factory name" in "English (United States)"
        And I specify its code as "PMWWFN"
        And I try to add it
        Then I should be notified that I have to specify factory name that is available

    @api @ui
    Scenario: Trying to remove name from an existing payment method
        When I want to modify the "Offline" payment method
        And I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this payment method should still be named "Offline"
