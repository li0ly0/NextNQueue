# NextNQueue

NextNQueue is a lightweight, dark-themed IT Helpdesk and Ticketing System designed for speed and simplicity. Built with a zero-build frontend stack, it offers a sleek monochromatic aesthetic and rapid performance.

**Note:** This project is provided strictly as a foundational template. The default UI might suck depending on your specific needs or tastes, but because it relies entirely on utility classes, it is highly customizable. Feel free to rip the Tailwind apart and make it your own!

## Features

### Admin Dashboard
* **Real-Time Analytics:** Quick-glance metrics for total, open, in-progress, high-priority, and urgent tickets.
* **Ticket Management:** Filter tickets by category (Hardware, Software, Network) and status. 
* **Aging Tracking:** Automatically calculates and displays how long a ticket has been open.
* **Private Admin Notes:** Document resolution steps securely without exposing them to the end user.
* **User Management:** Full CRUD interface for managing standard users and other administrators.

### User Portal
* **Intuitive Submission Form:** Easy-to-use interface for submitting new support requests with visual category selection.
* **Ticket Tracking:** Users can view a history of their submitted tickets and filter by active/closed status.

## Tech Stack

**Frontend:**
* HTML5
* [Tailwind CSS](https://tailwindcss.com/) (via CDN)
* [Alpine.js](https://alpinejs.dev/) (via CDN)
* [Lucide Icons](https://lucide.dev/)

**Backend (Required):**
* PHP (RESTful API endpoints)
* Relational Database (MySQL/SQLite)

## Installation & Setup

Currently, this repository contains the **frontend architecture**. To run this locally, you will need to implement the backend API endpoints.

1. Clone the repository:

        git clone https://github.com/yourusername/nextnqueue.git

2. Serve the directory using a local web server (e.g., PHP's built-in server):

        php -S localhost:8000

3. **Backend Implementation:** Create the following PHP files to handle the API requests:
   * `auth.php` - Handles `POST` requests for login validation. Returns a JSON user object.
   * `api.php` - Handles `GET`, `POST`, and `PATCH` requests for fetching, creating, and updating tickets.
   * `users.php` - Handles `GET`, `POST`, `PATCH`, and `DELETE` requests for user management.
