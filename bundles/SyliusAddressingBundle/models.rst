Models
======

Here is a quick reference for the default models.

Address, Country and Province
-----------------------------

This are three default models to represent address.
Address model itself is represented by first and last name, country, province, street, city and postcode.
Country is represented with name and ISO 3166-1 code, and can reference a collection of provinces.

Zone
----

Zones serve as a mechanism for grouping geographic areas together into a single entity. Each zone have name, type and a collection of zone members.
There are three zone types available: country, province and zone type.
If zone if of type country, it can consist of one or more countries, one example of this zone type can be EU.
Similar, zone can consist of one or more provinces if it is of type province. And finally, a zone can also consist of other zones.

ZoneMember
----------

There is a base model for zone members, and for each zone type, there is a derived zone member model:
*ZoneMemberCountry*, *ZoneMemberProvince* and *ZoneMemberZone*.
Each of them connects *Country*, *Province* and *Zone* models with *Zone* in a collection of zone members with common *ZoneMemberInterface* interface.
