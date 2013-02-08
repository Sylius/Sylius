Feature: User registration
    In order to track my buyings
    As a visitor
    I need to be able to create an account in store

    Scenario: Successfully creating account in store
        Given I am on the homepage
         When I follow "Register"
         When I fill in the following:
            | Email        | foo@bar.com |
            | Username     | foo         |
            | Password     | bar         |
            | Verification | bar         |
        And I press "register"
       Then I should see "Welcome"
        And I should see "Logout"

    Scenario: Trying to register with non verified password
        Given I am on the homepage
         When I follow "Register"
         When I fill in the following:
            | Email        | foo@bar.com |
            | Username     | foo         |
            | Password     | bar         |
            | Verification | foo         |
        And I press "register"
       Then I should be on registration page
        And I should see "The entered passwords don't match"
