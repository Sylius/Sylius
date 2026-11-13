@worker @worker:shipping @api
Feature: A channel created during process runtime can be used in a shipping method rule

    ChannelCodeCollectionValidator reads every channel code once and keeps the
    list in a property, so a worker validates every later submission against
    the channels that existed when the first one was checked. A channel created
    in the meantime looks unknown and its rule is rejected.

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @no-ui
    Scenario: A rule for a channel created after the first validation is accepted
        When I want to create a new shipping method
        And I specify its code as "FIRST"
        And I specify its position as 0
        And I name it "First" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add the "Items total greater than or equal" rule configured with $200 for "Web-US" channel
        And I add it
        Then the "First" shipping method should be successfully created
        Given the store operates on another channel named "Mobile" in "USD" currency
        When I want to create a new shipping method
        And I specify its code as "SECOND"
        And I specify its position as 1
        And I name it "Second" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I specify its amount as 50 for "Mobile" channel
        And I add it
        Then the "Second" shipping method should be successfully created
