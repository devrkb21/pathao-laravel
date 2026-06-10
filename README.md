<h1 align="center">Pathao Laravel — Complete Laravel Package for Pathao Courier API</h1>

<p align="center">
    <a href="https://packagist.org/packages/devrkb21/pathao-laravel"><img src="https://img.shields.io/packagist/v/devrkb21/pathao-laravel.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/devrkb21/pathao-laravel"><img src="https://img.shields.io/packagist/dt/devrkb21/pathao-laravel.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://github.com/devrkb21/pathao-laravel/blob/main/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
</p>

A complete Laravel package for [Pathao Courier Merchant API](https://merchant.pathao.com/) integration. Setup once and forget about it — built-in validation, webhook handling, location caching, API logging, and powerful Artisan commands.

## ✨ Features

- ✅ Client Credentials authentication with automatic token refresh
- ✅ Sandbox & Production environment support
- ✅ Cities, Zones, Areas — with bulk endpoints & local database caching
- ✅ Store management (List & Create)
- ✅ Order management (Create, View, Bulk Create, Price Calculation)
- ✅ Merchant profile info retrieval
- ✅ User success rate by phone number
- ✅ Webhook signature validation with Laravel event dispatching
- ✅ API request & webhook logging to database
- ✅ Built-in request validation for all POST endpoints
- ✅ Artisan commands for setup, sync, status, and diagnostics

---

## ⚙️ Installation

```bash
composer require devrkb21/pathao-laravel
```

Publish the config file:

```bash
php artisan vendor:publish --tag="pathao-config"
```

> Laravel auto-discovers the service provider. If needed, manually register:

```php
// config/app.php — providers array
devrkb21\PathaoLaravel\PathaoLaravelServiceProvider::class,

// config/app.php — aliases array
'PathaoLaravel' => devrkb21\PathaoLaravel\Facades\PathaoLaravel::class,
```

### Environment Variables

Add the following to your `.env` file:

```env
PATHAO_CLIENT_ID=
PATHAO_CLIENT_SECRET=
PATHAO_SECRET_TOKEN=
PATHAO_WEBHOOK_INTEGRATION_SECRET=
PATHAO_SANDBOX=false
PATHAO_DB_TABLE_NAME=pathao-courier
```

> **Where to find credentials?** Go to [Pathao Developers API](https://merchant.pathao.com/courier/developer-api) → Merchant API Credentials.

### Setup

Run the migration:

```bash
php artisan migrate
```

Set up authentication (one-time process):

```bash
php artisan pathao:setup
```

You will be provided a `secret_token`. Set it in your `.env` as `PATHAO_SECRET_TOKEN`.

---

## 🏗 Usage

```php
use devrkb21\PathaoLaravel\Facades\PathaoLaravel;
```

### Location APIs

```php
// Get all cities
PathaoLaravel::GET_CITIES();

// Get zones for a city
PathaoLaravel::GET_ZONES(int $city_id);

// Get areas for a zone
PathaoLaravel::GET_AREAS(int $zone_id);

// Bulk fetch all zones / areas
PathaoLaravel::GET_ZONES_BULK();
PathaoLaravel::GET_AREAS_BULK();
```

### Store APIs

```php
// List stores (with optional pagination)
PathaoLaravel::GET_STORES(int $page = 1);

// Create a new store
// Required: name, contact_name, contact_number, address, city_id, zone_id, area_id
PathaoLaravel::CREATE_STORE($request);
```

### Order APIs

```php
// Create an order
// Required: store_id, recipient_name, recipient_phone, recipient_address,
//           delivery_type (48=Normal, 12=On-Demand), item_type (1=Document, 2=Parcel),
//           item_quantity, item_weight, amount_to_collect
PathaoLaravel::CREATE_ORDER($request);

// Create multiple orders in bulk
PathaoLaravel::CREATE_ORDERS_BULK(array $orders);

// View order details
PathaoLaravel::VIEW_ORDER(string $consignment_id);

// Calculate delivery price
PathaoLaravel::GET_PRICE_CALCULATION($request);
```

### Merchant & User APIs

```php
// Get merchant profile info
PathaoLaravel::GET_MERCHANT_INFO();

// Get user success rate by phone
PathaoLaravel::GET_USER_SUCCESS_RATE($request);

// Check token expiry
PathaoLaravel::GET_ACCESS_TOKEN_EXPIRY_DAYS_LEFT();
```

---

## 🔧 Artisan Commands

| Command | Description |
| :--- | :--- |
| `pathao:setup` | Set up authentication credentials and generate secret token |
| `pathao:merchant-info` | Verify API connection and display merchant profile info |
| `pathao:sync-locations` | Fetch and cache all cities, zones, and areas into local database |
| `pathao:status` | Display connection config, token status, and cached location counts |
| `pathao:clear-cache` | Clear cached location tables |

---

## 🔔 Webhook Integration

The package automatically registers a webhook endpoint at:

```
POST /api/pathao/webhook
```

**Webhook Verification**: To verify your webhook URL on Pathao's merchant dashboard, the package automatically captures the required challenge token UUID and echoes it back in the `X-Pathao-Merchant-Webhook-Integration-Secret` header. If necessary, you can manually set this UUID in your `.env` file as `PATHAO_WEBHOOK_INTEGRATION_SECRET`.

**Signature Validation**: Incoming requests are validated against `X-Pathao-Signature` header using your `PATHAO_SECRET_TOKEN`.

**Event Dispatching**: On valid webhook, the package dispatches a `PathaoWebhookReceived` event. Listen for it in your application:

```php
use devrkb21\PathaoLaravel\Events\PathaoWebhookReceived;

Event::listen(PathaoWebhookReceived::class, function ($event) {
    $payload = $event->payload;

    // Map webhook data to your order status updates
    // $payload['event'], $payload['consignment_id'], $payload['order_status'], etc.
});
```

**Logging**: All webhook calls (valid and invalid) are logged to the `pathao_webhook_logs` table. All API requests are logged to `pathao_api_logs`.

---

## 📦 Database Tables

After running migrations, the following tables are created:

| Table | Purpose |
| :--- | :--- |
| `pathao-courier` (configurable) | Stores access tokens and secret keys |
| `pathao_cities` | Cached cities directory |
| `pathao_zones` | Cached zones directory |
| `pathao_areas` | Cached areas directory |
| `pathao_api_logs` | API request/response history |
| `pathao_webhook_logs` | Webhook callback history |

---

## Credits

- [Rakib Uddin](https://github.com/devrkb21)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
