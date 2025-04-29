# 🛒 Real-Time Orders & Analytics Backend

A lightweight backend system for managing orders, providing real-time analytics, AI-driven recommendations, and weather-based dynamic suggestions — all built **manually without ORMs or prebuilt frameworks**.

## 📦 Features

### 1. Order Management API
- `POST /orders`  
  Add a new order with the following fields:
  - `product_id`
  - `quantity`
  - `price`
  - `date`

- `GET /analytics`  
  Returns real-time sales insights:
  - 💰 Total revenue
  - 🔝 Top products by sales
  - ⏱ Revenue changes in the last 1 minute
  - 📦 Number of orders in the last 1 minute

---

### 2. Real-Time WebSocket Reporting
Clients can subscribe to real-time updates via WebSocket for:
- 📢 New orders
- 📊 Updated analytics

> WebSocket broadcasts are triggered when new orders are added.

---

### 3. AI-Powered Recommendations
- `GET /recommendations`  
  Sends recent sales data to an AI system (e.g., ChatGPT or Gemini) and returns:
  - Promotional suggestions
  - Strategic actions for higher revenue

> **Example AI Prompt:**  
> “Given this sales data, which products should we promote for higher revenue?”

---

### 4. Weather-Based Dynamic Suggestions
- Integration with **OpenWeather API** to:
  - Promote cold drinks on hot days ☀️
  - Promote hot drinks on cold days ❄️
  - Suggest dynamic pricing based on weather or seasonality

---

## 🛠 Tech Stack & Manual Constraints

- Backend Language:laravel
- Database:SQLite (file-based and lightweight)
- Real-Time:WebSocket (using SSE and AJAX)
- **AI API:** ChatGPT
- Weather API:Weatherapi

## 🚀 Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/real-time-orders-backend.git
cd real-time-orders-backend
