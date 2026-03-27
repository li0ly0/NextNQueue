# NextNQueue

NextNQueue is a streamlined, monochromatic IT Helpdesk and Ticketing System engineered for performance and operational efficiency. Designed with a zero-build frontend architecture, it ensures rapid deployment and high responsiveness across diverse hardware configurations. 

Note: This project is provided strictly as a foundational functional template. The default UI might suck depending on your specific needs or tastes, but because it relies entirely on utility classes, it is highly customizable. Feel free to make it your own!

---

## Features

### Administrative Dashboard
* **Real-Time Analytics:** Provides immediate metrics for total, open, in-progress, high-priority, and urgent support requests.
* **Ticket Management:** Enables filtering by operational category (Hardware, Software, Network) and resolution status. 
* **Aging Tracking:** Automatically calculates and displays the duration for which a ticket has remained active.
* **Private Administrative Notes:** Allows support staff to document resolution methodologies securely without exposing internal notes to the end user.
* **User Management:** Comprehensive CRUD (Create, Read, Update, Delete) interface for managing standard user accounts and administrative privileges.

### User Portal
* **Intuitive Submission Interface:** A streamlined form for submitting new support requests, featuring visual category selection to ensure accurate routing.
* **Request Tracking:** Grants users visibility into their historical and active support tickets.

---

## Technology Stack

* HTML5
* [Tailwind CSS](https://tailwindcss.com/) (Delivered via CDN)
* [Alpine.js](https://alpinejs.dev/) (Delivered via CDN)
* [Lucide Icons](https://lucide.dev/)
* PHP
* MySQL/MariaDB

---

## Database Schema



To initialize the backend, execute the following SQL schema to establish the necessary tables and indexes.

```sql
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `tickets`
-- --------------------------------------------------------

CREATE TABLE `tickets` (
  `id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `priority` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT 'open',
  `creator` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `admin_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Indexes and Auto-Increments
-- --------------------------------------------------------

ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;
```
---

## Installation & Setup

Currently, this repository provides the frontend architecture. To execute this application locally, you must configure the accompanying backend endpoints.

1. Clone the repository to your local environment:

        git clone https://github.com/li0ly0/nextnqueue.git

2. Initialize a local web server (e.g., utilizing PHP's built-in server):

        php -S localhost:8000
