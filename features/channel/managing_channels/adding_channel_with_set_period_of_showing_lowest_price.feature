@managing_channels
Feature: Specifying the lowest price for discounted products checking period while creating a channel
    In order to show lowest prices only from a specific period
    As an Administrator
    I want to add a new channel with the lowest price for discounted products checking period

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And the store operates in "United States" and "Poland"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new channel without specifying the lowest price for discounted products checking period
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I add it
        Then I should be notified that it has been successfully created
        And the "Mobile" channel should have the lowest price for discounted products checking period set to 30 days

    @api @ui
    Scenario: Adding a new channel with a specified lowest price for discounted products checking period
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I specify 15 days as the lowest price for discounted products checking period
        And I add it
        Then I should be notified that it has been successfully created
        And the "Mobile" channel should have the lowest price for discounted products checking period set to 15 days

    @api @ui
    Scenario: Being prevented from creating a new channel with the lowest price for discounted products checking period equal to zero
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I specify 0 days as the lowest price for discounted products checking period
        And I try to add it
        Then I should be notified that the lowest price for discounted products checking period must be greater than 0

    @api @ui
    Scenario: Being prevented from creating a new channel with a negative lowest price for discounted products checking period
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I disable showing the lowest price of discounted products
        And I specify -10 days as the lowest price for discounted products checking period
        And I try to add it
        Then I should be notified that the lowest price for discounted products checking period must be greater than 0

    @api @ui
    Scenario: Being prevented from creating a new channel with a too big lowest price for discounted products checking period
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I disable showing the lowest price of discounted products
        And I specify 99999999999 days as the lowest price for discounted products checking period
        And I try to add it
        Then I should be notified that the lowest price for discounted products checking period must be lower
