# Sahaj Mobile - Customer EMI Management API (Laravel)

A concise backend-only REST API for listing, filtering, sorting, paginating, and exporting customer EMI records.

- Runtime: PHP 8.1+ • Laravel 10
- Base URL (local): http://127.0.0.1:8000/api

## Overview

This API serves customer EMI data from a remote JSON source (configurable). It supports:

- Search by applicant or telephone
- Filter by status and shop_id
- Sort by id, originate_date, applicant, total_amount (asc/desc)
- Pagination (page, per_page)
- CSV export of filtered/sorted results
- Health check endpoint

## Quick Start

Requirements: PHP 8.1+, Composer

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
# Open: http://127.0.0.1:8000/api/health
```

## Configuration

Set these in `.env` (or leave defaults):

```env
# Remote JSON data source (publicly accessible URL)
CUSTOMERS_SOURCE_URL=https://your-remote-source/OUTPUT.json

# Cache TTL in seconds for remote fetch
CUSTOMERS_CACHE_TTL=300

# CORS (comma-separated origins)
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
```

Config files:

- `config/customers.php` – source_url, cache_ttl
- `config/cors.php` – allowed_origins

## API Reference

Base URL: `http://127.0.0.1:8000/api`

### 1) Get Customers (POST /get_customers_list)

Retrieve paginated, filtered, and sorted customers.

Body (JSON):

- page: integer (default 1)
- per_page: integer 1–100 (default 50)
- shop_id: integer (optional)
- status: string (optional, default "all")
- search: string (optional)
- sort_by: one of `originate_date`, `applicant`, `total_amount`, `id` (default `originate_date`)
- sort_order: `asc` | `desc` (default `desc`)

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/get_customers_list \
  -H "Content-Type: application/json" \
  -d '{
    "page": 1,
    "per_page": 20,
    "sort_by": "applicant",
    "sort_order": "asc"
  }'
```

Success response (200) shape:

```json
{
  "response": "success",
  "status": 1,
  "message": "Data retrieved successfully",
  "pagination": {
    "total_records": 50,
    "current_page": 1,
    "per_page": 20,
    "total_pages": 3
  },
  "data": [
    {
      /* customer */
    }
  ]
}
```

Errors:

- 422: invalid parameters (e.g., bad sort_by)
- 500: remote source unavailable or invalid

### 2) Export CSV (GET /customers/export)

Downloads a CSV of all matching records (no pagination).

Query params: `shop_id`, `status`, `search`, `sort_by`, `sort_order` (same as above)

Example:

```bash
curl -G "http://127.0.0.1:8000/api/customers/export" \
  --data-urlencode "status=Active" \
  --data-urlencode "sort_by=applicant" \
  --data-urlencode "sort_order=asc" \
  -o customers.csv
```

### 3) Health (GET /health)

Simple readiness probe.

```bash
curl http://127.0.0.1:8000/api/health
```

## Notes

- Data Source: The service fetches and caches the remote JSON. If it returns a wrapped structure (`{ data: [...] }`) or a flat array (`[...]`), both are supported.
- Sorting: Type-aware for dates and numbers. Allowed sort_by values are enforced by validation.
- CORS: Configure `CORS_ALLOWED_ORIGINS` for your frontend origins.

## Project Structure (Backend Only)

```
laravel-backend/
├── app/
│   ├── Http/Controllers/CustomerController.php
│   └── Services/CustomerService.php
├── config/
│   ├── cors.php
│   └── customers.php
├── routes/api.php
└── public/index.php
```

## License

MIT
