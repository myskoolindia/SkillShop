# RoboQuest Integration Notes — Orders API

**Base URL:** `https://devpro.skillvation.com/api`  
**Target Resource:** Institutional & Course Orders (`/admin/orders`)  
**Security Level:** Confidential — strict tenant isolation; unauthenticated requests are blocked with `HTTP 401`.

---

## 1. Endpoints

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` / `POST` | `/roboquest/orders` | List orders with pagination, search, and filters |
| `GET` | `/roboquest/orders/{id}` | Order details by Order ID (e.g. `69`) or Invoice ID (`INV-MYSKOOL-2026-001`) |
| `GET` / `POST` | `/roboquest/admin/orders` | Alias for orders list |
| `GET` | `/roboquest/admin/orders/{id}` | Alias for order details |

---

## 2. Authentication Methods

Choose the method that fits your integration architecture:

### Method A: School-Signed HMAC (Recommended for RoboQuest Server)
When the RoboQuest backend calls Skillvation on behalf of a school:
- **Headers:**
  - `X-RoboQuest-Signature`: `hash_hmac('sha256', requestContent, schoolSharedKey)`
  - `X-RoboQuest-School-Id`: `<school_id>` (e.g. `myskool-intl`)
  - `X-RoboQuest-Timestamp`: Current UTC UNIX timestamp (`time()`)
  - `X-RoboQuest-Nonce`: Random string (min 16 chars)
- **Data Scope:** Automatically isolated strictly to that school's own institutional orders and active members. Other schools' orders cannot be accessed.

### Method B: Sanctum Bearer Token (Recommended for Frontend / SSO)
When the school admin or educator is signed in via SSO or has an auth token:
- **Header:**
  - `Authorization: Bearer <SanctumToken>`
- **Data Scope:** Automatically scoped to the authenticated school account (`buyer_id = user.id`).

### Method C: Platform Key (Educator Console / Super Admin)
When RoboQuest platform backend queries orders across schools:
- **Header:**
  - `X-RoboQuest-Platform-Key: <ROBOQUEST_PLATFORM_KEY>` or `X-API-SECRET: <CRRM_API_SECRET>`
- **Data Scope:** Platform-wide, filterable by `school_id`, `buyer_id`, or `keyword`.

---

## 3. Query Parameters

All parameters are optional and work on both `GET` (query string) and `POST` (JSON body):

| Parameter | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `keyword` or `search` | `string` | — | Search invoice ID, buyer name, email, phone, or course title |
| `status` or `order_status` | `string` | — | `completed`, `processing`, `pending`, `declined` |
| `payment_status` | `string` | — | `paid`, `pending`, `cancelled` |
| `payment_method` | `string` | — | Filter by gateway (e.g. `razorpay`, `bank`) |
| `school_id` | `string` | — | Filter by school ID (admin callers only) |
| `email` | `string` | — | Filter by buyer email (within authorized scope) |
| `per_page` or `limit` | `int` | `20` | Items per page (1 to 100, or `all` for unpaginated) |
| `page` | `int` | `1` | Pagination page number |
| `sort_by` | `string` | `id` | `id`, `created_at`, `paid_amount`, `status` |
| `order_by` | `string` | `desc` | `desc` or `asc` |

---

## 4. Response Schema

```json
{
  "status": "success",
  "orders": [
    {
      "id": 69,
      "invoice_id": "INV-MYSKOOL-2026-001",
      "order_number": "INV-MYSKOOL-2026-001",
      "buyer_id": 44,
      "buyer": {
        "id": 44,
        "name": "Myskool International",
        "email": "myskool.international@myskool.club",
        "phone": "+919876543210",
        "school_name": "Myskool International",
        "role": "school",
        "image": "/uploads/website-images/frontend-avatar.png"
      },
      "user": { ... },
      "paid_amount": 236999.00,
      "payable_amount": 236999.00,
      "payable_currency": "INR",
      "currency": "INR",
      "payment_method": "razorpay",
      "gateway": "Razorpay",
      "status": "completed",
      "payment_status": "paid",
      "transaction_id": "txn_myskool_live_pb0HnbgKCj",
      "has_coupon": false,
      "coupon_code": null,
      "coupon_discount_amount": 0,
      "created_at": "2026-11-21 17:44:08",
      "date": "2026-11-21 17:44:08",
      "items_count": 11,
      "items": [
        {
          "id": 100,
          "order_id": 69,
          "course_id": 21,
          "title": "SV Lab with Infrastructure (Skillvation Lab)",
          "course_title": "SV Lab with Infrastructure (Skillvation Lab)",
          "course_slug": "sv-lab-with-infra",
          "api_course_id": null,
          "price": 150000.00,
          "commission_rate": 0
        },
        {
          "id": 101,
          "order_id": 69,
          "course_id": 22,
          "title": "CAMPS Complete Programme (Grades 1 to 5)",
          "course_title": "CAMPS Complete Programme (Grades 1 to 5)",
          "course_slug": "camps-complete-programme-grades-1-to-5",
          "api_course_id": null,
          "price": 25000.00,
          "commission_rate": 0
        }
      ]
    }
  ],
  "data": {
    "total": 38,
    "per_page": 20,
    "current_page": 1,
    "last_page": 2,
    "from": 1,
    "to": 20,
    "orders": [ ... ]
  },
  "pagination": {
    "total": 38,
    "per_page": 20,
    "current_page": 1,
    "last_page": 2
  }
}
```

---

## 5. Code Examples

### JavaScript / TypeScript (Next.js / Node.js)
```typescript
// Fetch orders with platform key or bearer token
async function getRoboquestOrders(page = 1, search = '') {
  const params = new URLSearchParams({ page: String(page), keyword: search });
  const response = await fetch(`https://devpro.skillvation.com/api/roboquest/orders?${params}`, {
    headers: {
      'Accept': 'application/json',
      'X-RoboQuest-Platform-Key': process.env.ROBOQUEST_PLATFORM_KEY || ''
      // Or: 'Authorization': `Bearer ${userToken}`
    }
  });

  if (!response.ok) {
    throw new Error(`Failed to load orders: ${response.status}`);
  }

  const result = await response.json();
  return result.orders; // Array of orders
}
```

### PHP (Server-to-Server HMAC)
```php
function fetchSchoolOrders($schoolId, $sharedKey, $page = 1) {
    $timestamp = time();
    $nonce = bin2hex(random_bytes(16));
    $body = json_encode(['page' => $page, 'schoolId' => $schoolId, 'timestamp' => $timestamp, 'nonce' => $nonce]);
    $signature = hash_hmac('sha256', $body, $sharedKey);

    $ch = curl_init('https://devpro.skillvation.com/api/roboquest/orders');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-RoboQuest-Signature: ' . $signature,
        ]
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}
```

---

## 6. Error Responses

| Code | Reason | Example Response |
| :--- | :--- | :--- |
| `401 Unauthorized` | Missing, invalid, or expired credentials | `{"status":"error","message":"Unauthorized. Order information is confidential and requires valid authentication credentials."}` |
| `404 Not Found` | Order does not exist or belongs to another school | `{"status":"error","message":"Order not found or access denied."}` |
| `429 Too Many Requests` | Rate limit exceeded (> 300 requests/minute) | Standard rate limit response |
