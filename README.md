# 🏦 Sahaj Mobile - Customer EMI Management Dashboard API

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-10.49.1-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4.1-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**A robust REST API for managing customer EMI (Equated Monthly Installment) financing data**

[Features](#-features) • [Quick Start](#-quick-start) • [API Reference](#-api-reference) • [Configuration](#-configuration)

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Quick Start](#-quick-start)
- [API Reference](#-api-reference)
- [Configuration](#-configuration)
- [Testing](#-testing-the-api)
- [Troubleshooting](#-troubleshooting)
- [Deployment](#-deployment)

---

## 🎯 Overview

Sahaj Mobile is a mobile phone financing company operating across Bangladesh. This Laravel-based REST API powers their operations dashboard, enabling efficient tracking and management of customer financing applications, payment status, and EMI collections.

**Key Capabilities:**

- Real-time customer data management
- Flexible filtering, searching, and sorting
- CSV export for reporting
- Remote data source integration with caching
- CORS-enabled for modern frontend frameworks

---

## ✨ Features

### 🔍 Data Operations

- **Search**: Find customers by applicant name or telephone number
- **Filter**: By status (Active, Pending, Overdue, Completed, Rejected) or shop ID
- **Sort**: By ID, Originate Date, Applicant Name, or Total Amount (asc/desc)
- **Paginate**: Flexible per-page limits (1-100 records)
- **Export**: Download filtered/sorted data as CSV

### 🎨 Status Management

Each customer record includes a color-coded status:

- 🟢 **Active** (success) - Regular payments ongoing
- 🟡 **Pending** (warning) - Awaiting approval/payment
- 🔴 **Overdue** (danger) - Payment overdue
- 🔵 **Completed** (info) - Fully paid
- ⚫ **Rejected** (secondary) - Application declined

### 🛡️ Technical Features

- ✅ RESTful JSON API
- ✅ Validation & error handling
- ✅ Remote data source with caching (configurable TTL)
- ✅ CORS support for cross-origin requests
- ✅ Health check endpoint
- ✅ Type-aware sorting (dates, numbers, strings)
- ✅ CSV streaming (no memory bloat)

---

## 🛠️ Tech Stack

| Technology      | Version  | Purpose               |
| --------------- | -------- | --------------------- |
| **Laravel**     | 10.49.1  | API framework         |
| **PHP**         | 8.4.1    | Runtime               |
| **Composer**    | 2.x      | Dependency management |
| **SQLite**      | 3.x      | Database (optional)   |
| **HTTP Client** | Built-in | Remote data fetching  |

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.1+ with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `curl`
- Composer 2.x
- Terminal (Bash/Zsh)

### Installation

1. **Clone & Navigate**

   ```bash
   cd /path/to/laravel-backend
   ```

2. **Install Dependencies**

   ```bash
   composer install
   ```

3. **Environment Setup**

   ```bash
   # Copy environment template
   cp .env.example .env

   # Generate application key
   php artisan key:generate
   ```

4. **Configure Remote Data Source** (optional)

   Edit `.env`:

   ```env
   # Remote JSON data source (leave empty to use generated mock data)
   CUSTOMERS_SOURCE_URL=https://gist.githubusercontent.com/...your-gist.../raw/OUTPUT.json

   # Cache TTL in seconds (default: 300)
   CUSTOMERS_CACHE_TTL=300

   # CORS allowed origins (comma-separated)
   CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
   ```

5. **Start Development Server**

   ```bash
   php artisan serve
   # Server starts at http://127.0.0.1:8000
   ```

6. **Verify Installation**
   ```bash
   curl http://127.0.0.1:8000/api/health
   # Expected: {"status":"ok","time":"2025-11-06T..."}
   ```

### 🎬 One-Line Setup (Linux/Mac)

```bash
composer install && cp .env.example .env && php artisan key:generate && php artisan serve
```

---

## 📡 API Reference

### Base URL

```
http://127.0.0.1:8000/api
```

### Endpoints

#### 1️⃣ Get Customers List (POST)

**Endpoint:** `POST /api/get_customers_list`

**Description:** Retrieve paginated, filtered, and sorted customer data.

**Request Body:**

```json
{
  "page": 1,
  "per_page": 50,
  "shop_id": 123,
  "status": "Active",
  "search": "Rahman",
  "sort_by": "originate_date",
  "sort_order": "desc"
}
```

**Parameters:**

| Parameter    | Type    | Required | Default          | Description                                                      |
| ------------ | ------- | -------- | ---------------- | ---------------------------------------------------------------- |
| `page`       | integer | No       | 1                | Page number (min: 1)                                             |
| `per_page`   | integer | No       | 50               | Records per page (1-100)                                         |
| `shop_id`    | integer | No       | null             | Filter by shop ID                                                |
| `status`     | string  | No       | "all"            | Filter by status (Active, Pending, Overdue, Completed, Rejected) |
| `search`     | string  | No       | ""               | Search applicant name or telephone                               |
| `sort_by`    | string  | No       | "originate_date" | Sort column: `originate_date`, `applicant`, `total_amount`, `id` |
| `sort_order` | string  | No       | "desc"           | Sort direction: `asc` or `desc`                                  |

**Success Response (200):**

```json
{
  "response": "success",
  "status": 1,
  "message": "Data retrieved successfully",
  "pagination": {
    "total_records": 50,
    "current_page": 1,
    "per_page": 50,
    "total_pages": 1
  },
  "data": [
    {
      "id": 1234,
      "originate_date": "2025-01-15",
      "month_week": "12 Months",
      "emi_package": "Standard",
      "installment": 2500,
      "installment_display": "৳2,500",
      "applicant": "Md. Rahman Khan",
      "telephone": "01712345678",
      "shop_name": "Sahaj Mobile - Dhaka",
      "shop_id": 123,
      "total_amount": 30000,
      "total_amount_display": "৳30,000",
      "paid": 15000,
      "paid_display": "৳15,000",
      "due": 15000,
      "due_display": "৳15,000",
      "last_pay_date": "2025-01-20",
      "status": "Active",
      "status_color": "success"
    }
  ]
}
```

**Error Response (500):**

```json
{
  "response": "error",
  "status": 0,
  "message": "Failed to retrieve data: Remote customer source unavailable or invalid.",
  "data": []
}
```

**Example cURL:**

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

---

#### 2️⃣ Export Customers to CSV (GET)

**Endpoint:** `GET /api/customers/export`

**Description:** Download all filtered/sorted customers as CSV (no pagination).

**Query Parameters:**

| Parameter    | Type    | Required | Default          | Description                   |
| ------------ | ------- | -------- | ---------------- | ----------------------------- |
| `shop_id`    | integer | No       | null             | Filter by shop ID             |
| `status`     | string  | No       | "all"            | Filter by status              |
| `search`     | string  | No       | ""               | Search applicant or telephone |
| `sort_by`    | string  | No       | "originate_date" | Sort column                   |
| `sort_order` | string  | No       | "desc"           | Sort direction                |

**Response:** CSV file download (`customers_YYYYMMDD_HHMMSS.csv`)

**CSV Headers:**

```
ID,Originate Date,Applicant,Telephone,Shop Name,EMI Package,Months/Weeks,Installment,Total Amount,Paid,Due,Last Pay Date,Status
```

**Example cURL:**

```bash
# Export all active customers sorted by applicant
curl -G "http://127.0.0.1:8000/api/customers/export" \
  --data-urlencode "status=Active" \
  --data-urlencode "sort_by=applicant" \
  --data-urlencode "sort_order=asc" \
  -o customers.csv

# Export with search filter
curl -G "http://127.0.0.1:8000/api/customers/export" \
  --data-urlencode "search=Rahman" \
  -o customers_rahman.csv
```

---

#### 3️⃣ Health Check (GET)

**Endpoint:** `GET /api/health`

**Description:** Simple readiness check for monitoring/load balancers.

**Response (200):**

```json
{
  "status": "ok",
  "time": "2025-11-06T14:23:45+00:00"
}
```

**Example cURL:**

```bash
curl http://127.0.0.1:8000/api/health
```

---

## ⚙️ Configuration

### Environment Variables

Create or edit `.env` in the project root:

```env
# Application
APP_NAME="Sahaj Mobile EMI API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Remote Data Source (Customers)
CUSTOMERS_SOURCE_URL=https://gist.githubusercontent.com/.../OUTPUT.json
CUSTOMERS_CACHE_TTL=300

# CORS Configuration
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000,https://yourdomain.com

# Database (optional - currently not used)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

### Configuration Files

#### `config/customers.php`

```php
return [
    'source_url' => env('CUSTOMERS_SOURCE_URL', ''),
    'cache_ttl' => env('CUSTOMERS_CACHE_TTL', 300),
];
```

#### `config/cors.php`

```php
'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://127.0.0.1:3000')),
```

### Remote Data Source Format

Your remote JSON should match this structure:

**Option 1 - Wrapped:**

```json
{
  "response": "success",
  "status": 1,
  "data": [
    { "id": 1234, "applicant": "...", ... }
  ]
}
```

**Option 2 - Flat Array:**

```json
[
  { "id": 1234, "applicant": "...", ... },
  { "id": 1235, "applicant": "...", ... }
]
```

---

## 🧪 Testing the API

### Quick Smoke Tests

Run these commands to verify all endpoints work:

```bash
# 1. Health check
curl http://127.0.0.1:8000/api/health

# 2. Get first page (default sort)
curl -X POST http://127.0.0.1:8000/api/get_customers_list \
  -H "Content-Type: application/json" \
  -d '{"page":1,"per_page":10}'

# 3. Search for specific customer
curl -X POST http://127.0.0.1:8000/api/get_customers_list \
  -H "Content-Type: application/json" \
  -d '{"search":"Rahman","page":1,"per_page":10}'

# 4. Sort by applicant ascending
curl -X POST http://127.0.0.1:8000/api/get_customers_list \
  -H "Content-Type: application/json" \
  -d '{"sort_by":"applicant","sort_order":"asc","page":1,"per_page":10}'

# 5. Filter by status
curl -X POST http://127.0.0.1:8000/api/get_customers_list \
  -H "Content-Type: application/json" \
  -d '{"status":"Active","page":1,"per_page":10}'

# 6. Export to CSV
curl -G "http://127.0.0.1:8000/api/customers/export" \
  --data-urlencode "sort_by=applicant" \
  -o test_export.csv && head -n 3 test_export.csv
```

### Expected Response Times

| Endpoint           | Typical Response | With Cache | Without Cache |
| ------------------ | ---------------- | ---------- | ------------- |
| Health Check       | < 10ms           | N/A        | N/A           |
| Get Customers List | 50-200ms         | 30-80ms    | 100-300ms     |
| CSV Export         | 100-500ms        | 80-200ms   | 200-600ms     |

---

## 🐛 Troubleshooting

### Common Issues

#### 1. "Remote customer source unavailable"

**Cause:** The configured `CUSTOMERS_SOURCE_URL` is unreachable or returns invalid JSON.

**Solutions:**

- Check your internet connection
- Verify the gist URL is publicly accessible
- Test the URL in a browser: `curl <YOUR_URL>`
- Clear Laravel cache: `php artisan cache:clear`
- Set `CUSTOMERS_SOURCE_URL=""` in `.env` to use generated mock data (fallback removed in production)

#### 2. CORS errors in browser console

**Cause:** Frontend origin not in allowed list.

**Solutions:**

- Add your frontend URL to `.env`:
  ```env
  CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
  ```
- Restart Laravel server after changing `.env`
- Check `config/cors.php` has correct settings

#### 3. 422 Validation Error on sort_by

**Cause:** Invalid sort column sent.

**Solutions:**

- Only use allowed values: `originate_date`, `applicant`, `total_amount`, `id`
- Check request is sending correct field names (lowercase, underscore-separated)

**Cause:** Invalid sort column sent.

**Solutions:**

- Only use allowed values: `originate_date`, `applicant`, `total_amount`, `id`
- Check frontend is sending correct field names (lowercase, underscore-separated)

#### 4. CSV export returns empty file

**Cause:** No records match the applied filters.

**Solutions:**

- Remove filters to export all data
- Verify remote data source is returning records
- Check Laravel logs: `tail -f storage/logs/laravel.log`

#### 5. Port 8000 already in use

**Solutions:**

```bash
# Use a different port
php artisan serve --port=8001

# Or kill the process using port 8000
lsof -ti:8000 | xargs kill -9
```

### Debug Mode

Enable detailed error messages in `.env`:

```env
APP_DEBUG=true
APP_ENV=local
```

**⚠️ Never enable debug mode in production!**

### Logs

Check application logs:

```bash
# View recent logs
tail -f storage/logs/laravel.log

# Clear old logs
> storage/logs/laravel.log
```

---

## 📂 Project Structure

```
laravel-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── CustomerController.php    # API endpoints
│   │   └── Middleware/
│   │       ├── Cors.php                  # CORS middleware
│   │       └── VerifyCsrfToken.php
│   └── Services/
│       └── CustomerService.php           # Business logic
├── config/
│   ├── cors.php                          # CORS configuration
│   ├── customers.php                     # Remote source config
│   ├── session.php
│   └── view.php
├── routes/
│   └── api.php                           # API route definitions
├── storage/
│   ├── app/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
│       └── laravel.log
├── .env                                  # Environment variables
├── composer.json                         # PHP dependencies
└── README.md                            # This file
```

---

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production CORS origins
- [ ] Use HTTPS for API and remote data source
- [ ] Set up proper logging/monitoring
- [ ] Configure cache driver (Redis recommended)
- [ ] Set `CUSTOMERS_CACHE_TTL` appropriately
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`

### Recommended Hosting

- **Laravel Forge** - Automated Laravel deployment
- **DigitalOcean App Platform** - Easy Laravel hosting
- **AWS EC2 + RDS** - Scalable infrastructure
- **Heroku** - Simple deployment with Heroku CLI

### Example Nginx Config

```nginx
server {
    listen 80;
    server_name api.sahajterminal.com;
    root /var/www/laravel-backend/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

## 👥 Support & Contact

For issues, questions, or contributions:

- 📧 Email: support@sahajterminal.com
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/laravel-backend/issues)
- 📖 Laravel Docs: [laravel.com/docs](https://laravel.com/docs)

---

## 📚 Additional Resources

- 📖 [Laravel Documentation](https://laravel.com/docs/10.x)
- 🔒 [Laravel Security Best Practices](https://laravel.com/docs/10.x/security)
- 🚀 [Laravel Deployment Guide](https://laravel.com/docs/10.x/deployment)
- 🔧 [Composer Documentation](https://getcomposer.org/doc/)

---

<div align="center">

### ⭐ If this project helps you, please give it a star!

**Built with ❤️ for Sahaj Mobile Bangladesh**

Laravel 10.49.1 • PHP 8.4.1 • 2025

[⬆ Back to Top](#-sahaj-mobile---customer-emi-management-dashboard-api)

</div>

### Sort Data

- Click on column headers (ID, Date, Applicant, Amount)
- First click: Descending order
- Second click: Ascending order
- Arrow icons show current sort state

### Filter by Status

- Click status dropdown
- Select desired status
- Table updates with filtered results

### Navigate Pages

- Use Previous/Next buttons
- Or click specific page numbers
- Shows records count at bottom

## 🔧 Configuration

### Backend Configuration

- **CORS**: `laravel-backend/config/cors.php`
- **Routes**: `laravel-backend/routes/api.php`
- **Mock Data**: `laravel-backend/app/Services/CustomerService.php`

### Frontend Configuration

- **API URL**: `nextjs-frontend/.env.local`
- **Records per page**: `nextjs-frontend/components/CustomerTable.tsx`
- **Styling**: `nextjs-frontend/tailwind.config.js`

## 🐛 Troubleshooting

### CORS Errors

- Check Laravel `config/cors.php` allows `http://localhost:3000`
- Restart Laravel server after config changes

### API Connection Failed

- Verify Laravel server is running on port 8000
- Check `.env.local` has correct API URL
- Check browser console for network errors

### Page Not Loading

- Clear browser cache
- Delete `.next` folder and restart frontend
- Check terminal for build errors

## 📦 Production Build

### Backend

```bash
cd laravel-backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
```

### Frontend

```bash
cd nextjs-frontend
npm run build
npm start
```

---

## 📚 Additional Resources

- 📖 Laravel Documentation: [laravel.com/docs](https://laravel.com/docs/10.x)
- 🎨 Tailwind CSS: [tailwindcss.com](https://tailwindcss.com)
- ⚡ Next.js: [nextjs.org/docs](https://nextjs.org/docs)

---

<div align="center">

### ⭐ Star this repo if it helped you!

**Made with ❤️ for Sahaj Mobile Bangladesh**

Laravel 10.49.1 • PHP 8.4.1 • 2025

[⬆ Back to Top](#-sahaj-mobile---customer-emi-management-dashboard-api)

</div>

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License.

## 👥 Authors

- Development Team - Sahaj Mobile

## 🙏 Acknowledgments

- Laravel Framework
- Next.js Team
- shadcn/ui Components
- Tailwind CSS

## 📞 Support

For issues or questions:

- Check individual README files in `laravel-backend/` and `nextjs-frontend/`
- Review troubleshooting section
- Contact development team

---

**Built with ❤️ for Sahaj Mobile**
# Sahaj_mobile_Backend
