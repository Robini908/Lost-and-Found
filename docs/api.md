# Lost & Found System API Documentation

This document provides a comprehensive guide to the RESTful API for the Lost & Found System. The API allows third-party integrations and programmatic access to the core functionality of the platform.

## Table of Contents

- [Authentication](#authentication)
- [Base URL](#base-url)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Rate Limiting](#rate-limiting)
- [Endpoints](#endpoints)
  - [User Management](#user-management)
  - [Item Management](#item-management)
  - [Matching](#matching)
  - [Claims](#claims)
  - [Communication](#communication)
  - [Rewards](#rewards)
  - [Webhooks](#webhooks)
- [Examples](#examples)

## Authentication

The API uses OAuth 2.0 for authentication. To access protected endpoints, you must include a valid access token in the Authorization header of your requests.

### Obtaining an Access Token

```
POST /oauth/token
```

#### Request Body

```json
{
  "grant_type": "client_credentials",
  "client_id": "your-client-id",
  "client_secret": "your-client-secret",
  "scope": "read write"
}
```

#### Response

```json
{
  "token_type": "Bearer",
  "expires_in": 3600,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "refresh_token": "def50200641f3e77b67467b..."
}
```

### Using the Access Token

Include the access token in the Authorization header of your requests:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

## Base URL

All API requests should be made to:

```
https://api.lostandfound.com/v1
```

For local development:

```
http://localhost:8000/api/v1
```

## Response Format

All responses are returned in JSON format. Successful responses have a 2xx status code and follow this structure:

```json
{
  "status": "success",
  "data": {
    // Response data varies by endpoint
  },
  "meta": {
    // Pagination, filters, etc.
  }
}
```

## Error Handling

Errors are returned with appropriate HTTP status codes and follow this structure:

```json
{
  "status": "error",
  "error": {
    "code": "error_code",
    "message": "Human-readable error message",
    "details": {
      // Additional error details (optional)
    }
  }
}
```

Common error codes:

| Status Code | Description |
|-------------|-------------|
| 400 | Bad Request - Invalid inputs |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource does not exist |
| 422 | Unprocessable Entity - Validation errors |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Server Error - Something went wrong on our end |

## Rate Limiting

The API implements rate limiting to ensure fair usage. Current limits are:

- 60 requests per minute for authenticated users
- 15 requests per minute for unauthenticated requests

Rate limit information is included in the response headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
X-RateLimit-Reset: 1620000000
```

## Endpoints

### User Management

#### Get Current User

```
GET /users/me
```

Retrieves the authenticated user's profile information.

**Response Example:**

```json
{
  "status": "success",
  "data": {
    "id": 123,
    "name": "John Doe",
    "email": "john@example.com",
    "profile_photo_url": "https://example.com/photos/john.jpg",
    "reward_points": 150,
    "created_at": "2023-01-15T09:24:17Z"
  }
}
```

#### List Users (Admin Only)

```
GET /users
```

Retrieves a paginated list of users. Requires admin permissions.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page |
| search | string | null | Search by name or email |
| role | string | null | Filter by role (admin, moderator, user) |

**Response Example:**

```json
{
  "status": "success",
  "data": [
    {
      "id": 123,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "created_at": "2023-01-15T09:24:17Z"
    },
    // Additional users...
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "total_pages": 4
  }
}
```

### Item Management

#### List Lost Items

```
GET /items/lost
```

Retrieves a paginated list of lost items.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page |
| category_id | integer | null | Filter by category |
| location_lat | float | null | Location latitude (for proximity search) |
| location_lng | float | null | Location longitude (for proximity search) |
| radius | integer | 10 | Proximity search radius in kilometers |
| date_lost_start | date | null | Filter by date lost (start range) |
| date_lost_end | date | null | Filter by date lost (end range) |
| search | string | null | Search by item title or description |

**Response Example:**

```json
{
  "status": "success",
  "data": [
    {
      "id": 456,
      "uuid": "los_a1b2c3d4e5f6",
      "title": "Lost iPhone 14 Pro",
      "description": "Black iPhone 14 Pro with red case lost in Central Park",
      "user_id": 123,
      "category_id": 5,
      "category_name": "Electronics",
      "location": {
        "latitude": 40.7812,
        "longitude": -73.9665,
        "address": "Central Park, New York, NY"
      },
      "date_lost": "2023-05-10",
      "has_images": true,
      "images": [
        {
          "id": 789,
          "url": "https://example.com/items/iphone-1.jpg",
          "thumbnail": "https://example.com/items/thumbs/iphone-1.jpg"
        }
      ],
      "status": "active",
      "has_monetary_reward": true,
      "reward_amount": 50.00,
      "currency": "USD",
      "created_at": "2023-05-11T14:32:00Z"
    },
    // Additional items...
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 120,
    "total_pages": 8
  }
}
```

#### Get Item Details

```
GET /items/{uuid}
```

Retrieves detailed information about a specific item.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| uuid | string | The item's UUID (required) |

**Response Example:**

```json
{
  "status": "success",
  "data": {
    "id": 456,
    "uuid": "los_a1b2c3d4e5f6",
    "title": "Lost iPhone 14 Pro",
    "description": "Black iPhone 14 Pro with red case lost in Central Park",
    "user": {
      "id": 123,
      "name": "John Doe"
    },
    "category_id": 5,
    "category_name": "Electronics",
    "location": {
      "latitude": 40.7812,
      "longitude": -73.9665,
      "address": "Central Park, New York, NY",
      "area": "Manhattan",
      "landmarks": "Near Bethesda Fountain"
    },
    "date_lost": "2023-05-10",
    "has_images": true,
    "images": [
      {
        "id": 789,
        "url": "https://example.com/items/iphone-1.jpg",
        "thumbnail": "https://example.com/items/thumbs/iphone-1.jpg"
      }
    ],
    "attributes": [
      {
        "key": "color",
        "value": "black"
      },
      {
        "key": "model",
        "value": "iPhone 14 Pro"
      }
    ],
    "tags": ["iphone", "smartphone", "black", "central park"],
    "status": "active",
    "has_monetary_reward": true,
    "reward_amount": 50.00,
    "currency": "USD",
    "expires_at": "2023-08-11T14:32:00Z",
    "created_at": "2023-05-11T14:32:00Z",
    "updated_at": "2023-05-11T14:32:00Z"
  }
}
```

#### Create Lost Item

```
POST /items/lost
```

Creates a new lost item report.

**Request Body:**

```json
{
  "title": "Lost iPhone 14 Pro",
  "description": "Black iPhone 14 Pro with red case lost in Central Park",
  "category_id": 5,
  "location_lat": 40.7812,
  "location_lng": -73.9665,
  "location_address": "Central Park, New York, NY",
  "area": "Manhattan",
  "landmarks": "Near Bethesda Fountain",
  "date_lost": "2023-05-10",
  "additional_details": {
    "color": "black",
    "model": "iPhone 14 Pro",
    "distinguishing_features": "Has a crack on the upper right corner"
  },
  "has_monetary_reward": true,
  "reward_amount": 50.00,
  "currency": "USD",
  "is_public": true,
  "tags": ["iphone", "smartphone", "black", "central park"]
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 456,
    "uuid": "los_a1b2c3d4e5f6",
    "upload_url": "https://example.com/api/v1/items/los_a1b2c3d4e5f6/images/upload",
    "message": "Lost item report created successfully"
  }
}
```

#### Upload Item Image

```
POST /items/{uuid}/images
```

Uploads an image for an item. Multiple images can be uploaded by making multiple requests.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| uuid | string | The item's UUID (required) |

**Request Body:**

Multipart form data with an image file under the key 'image'.

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 789,
    "url": "https://example.com/items/iphone-1.jpg",
    "thumbnail": "https://example.com/items/thumbs/iphone-1.jpg",
    "message": "Image uploaded successfully"
  }
}
```

### Matching

#### Get Potential Matches

```
GET /items/{uuid}/matches
```

Retrieves potential matches for a lost or found item.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| uuid | string | The item's UUID (required) |

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 10 | Items per page |
| min_score | float | 0.35 | Minimum match confidence score (0-1) |

**Response Example:**

```json
{
  "status": "success",
  "data": [
    {
      "id": 789,
      "uuid": "fnd_g7h8i9j0k1l2",
      "title": "Found iPhone",
      "description": "Found black iPhone in Central Park",
      "category_name": "Electronics",
      "location": {
        "address": "Central Park, New York, NY",
        "distance": 0.3
      },
      "date_found": "2023-05-11",
      "image_url": "https://example.com/items/thumbs/found-iphone.jpg",
      "match_score": 0.87,
      "match_details": {
        "description_similarity": 0.85,
        "category_match": 1.0,
        "location_proximity": 0.98,
        "date_proximity": 0.90,
        "attributes_match": 0.75
      },
      "created_at": "2023-05-11T18:45:00Z"
    },
    // Additional matches...
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 3,
    "total_pages": 1
  }
}
```

### Claims

#### Create Claim

```
POST /items/{uuid}/claims
```

Creates a claim for a found item.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| uuid | string | The found item's UUID (required) |

**Request Body:**

```json
{
  "lost_item_uuid": "los_a1b2c3d4e5f6",
  "claim_details": "I lost my iPhone in Central Park on May 10th. It's a black iPhone 14 Pro with a red case.",
  "verification_answers": [
    {
      "question_id": 1,
      "answer": "There's a distinctive scratch on the back near the camera."
    },
    {
      "question_id": 2,
      "answer": "The lock screen wallpaper is a beach sunset."
    }
  ],
  "item_attributes": {
    "color": "black",
    "model": "iPhone 14 Pro",
    "serial_number": "IMEI3546546546",
    "lock_screen": "Beach sunset photo"
  },
  "location_details": "I lost it near the Bethesda Fountain in Central Park",
  "time_lost": "2023-05-10T15:30:00Z"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 101,
    "uuid": "clm_m3n4o5p6q7r8",
    "status": "pending_review",
    "verification_score": 68,
    "message": "Claim submitted successfully and awaiting review by an administrator"
  }
}
```

#### Get Claim Status

```
GET /claims/{uuid}
```

Retrieves the status of a specific claim.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| uuid | string | The claim's UUID (required) |

**Response Example:**

```json
{
  "status": "success",
  "data": {
    "id": 101,
    "uuid": "clm_m3n4o5p6q7r8",
    "item_uuid": "fnd_g7h8i9j0k1l2",
    "lost_item_uuid": "los_a1b2c3d4e5f6",
    "status": "approved",
    "verification_score": 85,
    "created_at": "2023-05-12T10:15:00Z",
    "updated_at": "2023-05-13T09:24:00Z",
    "verified_at": "2023-05-13T09:24:00Z",
    "handover_info": {
      "contact_method": "in_app_messaging",
      "instructions": "Please use the messaging system to arrange a meet-up."
    }
  }
}
```

### Communication

#### List Messages

```
GET /messages
```

Retrieves a paginated list of messages for the authenticated user.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 20 | Items per page |
| item_uuid | string | null | Filter by related item |
| unread_only | boolean | false | Show only unread messages |

**Response Example:**

```json
{
  "status": "success",
  "data": [
    {
      "id": 201,
      "conversation_id": "conv_s9t0u1v2w3x4",
      "sender_id": 456,
      "sender_name": "Jane Smith",
      "item_uuid": "fnd_g7h8i9j0k1l2",
      "content": "Hi, I found your iPhone. Can we arrange a meeting to return it?",
      "message_type": "text",
      "is_read": false,
      "created_at": "2023-05-13T14:30:00Z"
    },
    // Additional messages...
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 5,
    "total_pages": 1,
    "unread_count": 2
  }
}
```

#### Send Message

```
POST /messages
```

Sends a message to another user regarding an item.

**Request Body:**

```json
{
  "recipient_id": 456,
  "item_uuid": "fnd_g7h8i9j0k1l2",
  "content": "Thank you for finding my iPhone. Can we meet tomorrow at 2 PM?",
  "message_type": "text"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 202,
    "conversation_id": "conv_s9t0u1v2w3x4",
    "content": "Thank you for finding my iPhone. Can we meet tomorrow at 2 PM?",
    "created_at": "2023-05-13T15:05:00Z",
    "message": "Message sent successfully"
  }
}
```

### Rewards

#### Get Reward History

```
GET /rewards
```

Retrieves the reward history for the authenticated user.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | integer | 1 | Page number |
| per_page | integer | 15 | Items per page |

**Response Example:**

```json
{
  "status": "success",
  "data": [
    {
      "id": 301,
      "reward_type": "item_return",
      "points_awarded": 50,
      "item": {
        "uuid": "fnd_g7h8i9j0k1l2",
        "title": "Found iPhone"
      },
      "monetary_amount": 50.00,
      "monetary_currency": "USD",
      "status": "processed",
      "processed_at": "2023-05-14T09:15:00Z"
    },
    // Additional rewards...
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 3,
    "total_pages": 1,
    "total_points": 150
  }
}
```

### Webhooks

#### Register Webhook

```
POST /webhooks
```

Registers a webhook to receive notifications about specific events.

**Request Body:**

```json
{
  "target_url": "https://your-service.example.com/webhook-receiver",
  "events": [
    "item.created",
    "item.matched",
    "claim.approved"
  ],
  "secret": "your-webhook-secret"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 401,
    "target_url": "https://your-service.example.com/webhook-receiver",
    "events": [
      "item.created",
      "item.matched",
      "claim.approved"
    ],
    "message": "Webhook registered successfully"
  }
}
```

#### List Webhooks

```
GET /webhooks
```

Lists all registered webhooks for the authenticated application.

**Response Example:**

```json
{
  "status": "success",
  "data": [
    {
      "id": 401,
      "target_url": "https://your-service.example.com/webhook-receiver",
      "events": [
        "item.created",
        "item.matched",
        "claim.approved"
      ],
      "created_at": "2023-05-15T11:30:00Z"
    },
    // Additional webhooks...
  ]
}
```

## Examples

### Example: Finding Potential Matches

This example shows how to find potential matches for a lost item:

```javascript
// Using fetch API
const fetchMatches = async (itemUuid, minScore = 0.5) => {
  const response = await fetch(`https://api.lostandfound.com/v1/items/${itemUuid}/matches?min_score=${minScore}`, {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
      'Content-Type': 'application/json'
    }
  });
  
  const data = await response.json();
  
  if (data.status === 'success') {
    return data.data;
  } else {
    throw new Error(data.error.message);
  }
};

// Usage
fetchMatches('los_a1b2c3d4e5f6')
  .then(matches => {
    console.log(`Found ${matches.length} potential matches`);
    matches.forEach(match => {
      console.log(`Match score: ${match.match_score} - ${match.title}`);
    });
  })
  .catch(error => {
    console.error('Error fetching matches:', error);
  });
```

### Example: Creating a Lost Item Report

This example shows how to create a new lost item report:

```javascript
// Using fetch API
const reportLostItem = async (itemData) => {
  const response = await fetch('https://api.lostandfound.com/v1/items/lost', {
    method: 'POST',
    headers: {
      'Authorization': 'Bearer YOUR_ACCESS_TOKEN',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(itemData)
  });
  
  const data = await response.json();
  
  if (data.status === 'success') {
    return data.data;
  } else {
    throw new Error(data.error.message);
  }
};

// Usage
const itemData = {
  title: "Lost MacBook Pro",
  description: "Silver MacBook Pro 16-inch lost in Central Station",
  category_id: 5,
  location_lat: 40.7527,
  location_lng: -73.9772,
  location_address: "Grand Central Terminal, New York, NY",
  date_lost: "2023-05-20",
  additional_details: {
    color: "silver",
    model: "MacBook Pro 16-inch",
    identifiers: "Has a black case with stickers"
  },
  has_monetary_reward: true,
  reward_amount: 100.00,
  currency: "USD"
};

reportLostItem(itemData)
  .then(result => {
    console.log(`Lost item reported successfully. UUID: ${result.uuid}`);
    
    // Now we can upload an image
    if (result.upload_url) {
      // Image upload logic...
    }
  })
  .catch(error => {
    console.error('Error reporting lost item:', error);
  });
```

## Contact Information

For questions, support, or bug reports related to the API, please contact:

Email: [abrahamopuba@gmail.com](mailto:abrahamopuba@gmail.com)

We aim to respond to all inquiries within 48 hours. 
