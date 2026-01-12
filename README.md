# Pertamina ATCS - Fullstack Monitoring System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Next.js](https://img.shields.io/badge/Next.js-15.x-000000?style=for-the-badge&logo=next.js)](https://nextjs.org)
[![Filament](https://img.shields.io/badge/Filament-v4-FFAD00?style=for-the-badge&logo=filament)](https://filamentphp.com)
[![Streaming](https://img.shields.io/badge/Streaming-NodeMediaServer-red?style=for-the-badge)](https://github.com/illuspas/Node-Media-Server)

A sophisticated, state-of-the-art monitoring and management system for **PT Kilang Pertamina Internasional – Refinery Unit VI Balongan**. This application integrates real-time CCTV streaming, Area Traffic Control System (ATCS) analytics, and infrastructure performance monitoring into a single, cohesive dashboard.

---

## 🏗️ Project Architecture

```
.
├── backend-new/                 # Laravel 12 + Filament v4 Backend (Core API & Admin)
│   ├── app/                     # Business logic, Models, and Services
│   ├── database/                # Migrations, Seeders, and Factories
│   ├── routes/                  # API and Web route definitions
│   └── public/                  # Assets and entry point
├── pertamina-frontend-build/    # Next.js 15 Source Code (Modern UI)
│   ├── app/                     # App Router pages and layouts
│   ├── components/              # Reusable React components
│   └── lib/                     # API clients and utilities
├── streaming-server/            # Node Media Server (CCTV Streaming Engine)
│   ├── server.js                # RTMP to HLS conversion logic
│   └── streams/                 # Temporary storage for HLS segments
└── v0-pertamina-frontend-build/ # (Legacy/Build Mirror)
```

---

## 🚀 Core Features

### 🚦 ATCS (Area Traffic Control System)
*   **Smart Grouping**: Productivity and performance metrics are grouped by **Building & Room**, allowing for area-based analysis rather than just per-unit.
*   **Performance Trends**: Multi-line charts tracking Traffic Volume, Average Speed, Congestion Index, and Green Wave Efficiency.
*   **Unit Performance**: Aggregated bar charts showing System Efficiency across different refinery zones.
*   **Manual Data Priority**: Admin can input real-world data via Filament, which automatically takes precedence over simulated data for high accuracy.

### 📹 CCTV & Streaming
*   **Live Monitoring**: Seamless HLS streaming with low latency.
*   **Conversion Engine**: Automatic RTMP to HLS conversion using FFmpeg on the streaming server.
*   **Health Status**: Real-time status monitoring (Online/Offline) of CCTV units.
*   **History Logs**: Record specific performance dates for each CCTV unit via the ATCS History feature.

### �️ Interactive Maps
*   **Geospatial Tracking**: Integrated Leaflet.js map showing locations of Buildings and CCTV units.
*   **Live Status Tooltips**: Clickable markers with real-time performance and status data.

### 🛠️ Admin Panel (Filament v4)
*   **Granular Management**: Full CRUD for Buildings, Rooms, and CCTV units.
*   **ATCS Entry**: Integrated Repeater forms for entering daily/hourly traffic and performance data.
*   **Role-Based Access**: Secure management interface for refinery operators.

---

## 🛠️ Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend** | Laravel 12, PHP 8.2+, MySQL |
| **Admin UI** | Filament v4 (TALL Stack) |
| **Frontend** | Next.js 15, React, TypeScript |
| **Charts** | Recharts (Responsive & Dynamic) |
| **Styling** | Tailwind CSS (Modern Glassmorphism Design) |
| **Maps** | Leaflet.js |
| **Streaming** | Node.js, NodeMediaServer, FFmpeg |

---

## 📦 Installation & Setup

### 1. Backend Setup
```bash
cd backend-new
composer install
cp .env.example .env
# Configure your DB_DATABASE, DB_USERNAME, etc. in .env
php artisan key:generate
php artisan migrate --seed
# Initial Seeders include: SuperAdminSeeder, RolePermissionSeeder
```

### 2. Frontend Setup
```bash
cd pertamina-frontend-build
npm install
npm run dev
```

### 3. Streaming Server
```bash
cd streaming-server
npm install
node server.js
```
*Note: Ensure **FFmpeg** is installed on your system and added to your environment PATH.*

---

## 🌐 Access Points

| Component | URL |
| :--- | :--- |
| **Main Dashboard** | [http://127.0.0.1:8000](http://127.0.0.1:8000) |
| **Admin Panel** | [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin) |
| **API Backend** | [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api) |
| **Streaming API** | [http://127.0.0.1:3002/api](http://127.0.0.1:3002/api) |

---

## 🔧 Developer Workflow

### Adding New ATCS Data
1.  Login to the **Admin Panel**.
2.  Navigate to **CCTVs**.
3.  Select a CCTV unit and go to the **ATCS History** section.
4.  Add a new date entry and fill in the metrics:
    *   *Traffic Volume*, *Avg Speed*, *Congestion Index*, *Green Wave Eff.*
5.  Save changes. The frontend charts (**Area Traffic Control System** and **Unit Performance**) will automatically update and aggregate this data based on the CCTV's Room/Building.

### CCTV Stream Configuration
-   Edit a CCTV record and provide the **RTSP URL** from the camera.
-   The system will automatically generate a stream ID (e.g., `cctv-1`).
-   The frontend will request the stream via the Node Media Server at `http://localhost:8000/live/cctv-1/index.m3u8`.

---

## 📱 Responsive & Premium Design
This application features a **Premium Glassmorphism UI** optimized for:
-   **Desktop**: For centralized control room monitoring.
-   **Mobile/Tablet**: For engineers on the field (Optimized charts, sliding sidebars, and responsive footers).

---

## 🔒 Security & Performance
-   **CORS Protection**: Configured for secure frontend-backend communication.
-   **Service Caching**: Production trends and unit performance calculations are cached to ensure sub-second response times.
-   **Real-time Prioritization**: Manual history inputs always override simulated fallback data.

---

&copy; 2026 **PT Kilang Pertamina Internasional – Refinery Unit VI Balongan**.  
All rights reserved.