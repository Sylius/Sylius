# UPGRADE FROM `2.2` TO `2.3`

## DateTime Serialization Format

The `sylius_api.normalizer.date_time` service has been removed. Previously, Sylius registered a custom
`DateTimeNormalizer` that overrode Symfony's built-in normalizer globally, forcing the `Y-m-d H:i:s` format
on all `DateTime` fields across the entire API.

All `DateTime` fields are now serialized using Symfony's default `DateTimeNormalizer`, which produces
the RFC 3339 format (`Y-m-d\TH:i:sP`).

**Before:**
```json
{
    "startDate": "2022-01-01 00:00:00",
    "endDate": "2022-01-02 00:00:00",
    "birthday": "2023-10-24 11:00:00",
    "expiresAt": "2020-01-01 12:00:00"
}
```

**After:**
```json
{
    "startDate": "2022-01-01T00:00:00+00:00",
    "endDate": "2022-01-02T00:00:00+00:00",
    "birthday": "2023-10-24T11:00:00+00:00",
    "expiresAt": "2020-01-01T12:00:00+00:00"
}
```

**Affected fields (non-exhaustive):** `startDate`, `endDate`, `startsAt`, `endsAt`, `expiresAt`, `birthday`,
`createdAt`, `updatedAt`, `archivedAt` and any other `DateTime` field exposed via the API.

**Migration guide:**

If your API client depends on the old `Y-m-d H:i:s` format, update it to parse RFC 3339 dates instead.
RFC 3339 is the standard format for REST APIs and is natively supported by all modern date libraries.

If you need to restore the old format or apply a custom one globally, register your own `DateTimeNormalizer`
with a higher priority than Symfony's default (`-910`):

```php
// config/services.php
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

$services
    ->set('app.normalizer.date_time', DateTimeNormalizer::class)
    ->args([['datetime_format' => 'Y-m-d H:i:s']])
    ->tag('serializer.normalizer', ['priority' => 1])
;
```

Alternatively, you may set the format for a specific field only by adding a serialization context
to the attribute definition in your serialization XML:

```xml
<attribute name="startDate">
    <context>
        <entry name="datetime_format">Y-m-d</entry>
    </context>
</attribute>
```
