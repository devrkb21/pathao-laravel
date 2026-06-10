# Changelog

All notable changes to `devrkb21/pathao-laravel` will be documented in this file.

## v1.0.0 - 2026-06-11

### 🎉 Initial Release

- Client Credentials authentication flow (`/aladdin/api/v1/external/login`)
- Sandbox & Production environment support
- Full location management: Cities, Zones, Areas (with bulk endpoints)
- Local database caching for locations (SQLite/MySQL)
- Store management: List & Create stores
- Order management: Create, View, Bulk Create, Price Calculation
- User success rate by phone number
- Merchant profile info retrieval
- Webhook signature validation with `PathaoWebhookReceived` event
- API request & webhook logging to database
- Artisan commands: `pathao:setup`, `pathao:merchant-info`, `pathao:sync-locations`, `pathao:status`, `pathao:clear-cache`
- Built-in request validation for all POST endpoints
