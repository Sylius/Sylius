@legacy @user
Feature: Forgot password
    In order to login to my account after a long time
    As a visitor
    I need to be able to reset my password

    Background:
        Given store has default configuration
        And there are following users:
            | email       | password | enabled |
            | bar@foo.com | foo1     | yes     |

    Scenario: Resetting user password
        Given I am on the store homepage
        And I follow "Login"
        And I follow "Forgot password"
        When I fill in "Email" with "bar@foo.com"
        And I press "Reset"
        Then I should be redirected to user login page
        And I should see "If the email you have specified exists in our system, we have sent there an instruction on how to reset your password"

    Scenario: Trying to reset password without email
        Given I am on the sylius user request password reset token page
        When I press "Reset"
        Then I should be on the sylius user request password reset token page
        And I should see "Please enter your email"

    Scenario: Trying to reset password with invalid email
        Given I am on the sylius user request password reset token page
        And I fill in "Email" with "invalidEmail"
        When I press "Reset"
        Then I should be on the sylius user request password reset token page
        And I should see "This email is invalid"

    Scenario: Trying to reset password for not existing email
        Given I am on the sylius user request password reset token page
        And I fill in "Email" with "foo@foo.com"
        When I press "Reset"
        Then I should be redirected to user login page
        And I should see "If the email you have specified exists in our system, we have sent there an instruction on how to reset your password"
