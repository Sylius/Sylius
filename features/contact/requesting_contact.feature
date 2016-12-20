@requesting_contact
Feature: Requesting contact
    In order to receive help from the store's support
    As a Customer
    I want to be able to send a message to the store's support

    Background:
        Given the store operates on a single channel in "United States"
        And this channel has contact email set as "contact@goodshop.com"

    @ui @email
    Scenario: Requesting contact as a logged in customer
        Given there is a user "lucifer@morningstar.com"
        And I am logged in as "lucifer@morningstar.com"
        When I want to request contact
        And I specify the message as "Hi! I did not receive an item!"
        And I send it
        Then I should be notified that the contact request has been submitted successfully
        And the email with contact request should be sent to "contact@goodshop.com"

    @ui @email
    Scenario: Requesting contact as a guest
        When I want to request contact
        And I specify the email as "lucifer@morningstar.com"
        And I specify the message as "Hi! I did not receive an item!"
        And I send it
        Then I should be notified that the contact request has been submitted successfully
        And the email with contact request should be sent to "contact@goodshop.com"
