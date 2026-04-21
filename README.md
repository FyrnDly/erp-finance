# ERP Finance

**ERP-Finance** is a comprehensive Enterprise Resource Planning (ERP) system tailored for financial management. It automates complex accounting workflows, ensures real-time data accuracy, and streamlines the bridge between operational tasks and financial reporting. Built with a modern tech stack, it emphasizes data integrity through automated balancing and strict Role-Based Access Control (RBAC).

## 🚀 Key Features

### 1. Advanced Ledger Management
* **Dynamic Chart of Accounts (CoA)**: Supports hierarchical account structures with automated prefix-based code generation for Assets, Liabilities, Equity, Revenue, and Expenses.
* **Wizard-Driven Journal Entries**: Features a multi-step wizard to guide users through complex financial recordings[cite: 2].
* **Real-time Balance Validation**: Implements a strict validation engine that prevents saving any journal entry where Total Debit does not equal Total Credit[cite: 2].
* **Automated Bookkeeping**: Transactions from operational modules like Invoices and Expenses automatically trigger background journal entries, ensuring the ledger is always up-to-date[cite: 1].

### 2. Operational Modules
* **Expense Management**: Allows staff to submit expenditure requests with a dedicated verification flow for managers to approve and liquidate funds.
* **Invoice & Revenue Tracking**: Manages client billings with reactive subtotal calculations and automated revenue recognition upon payment[cite: 1].
* **Audit-Ready History**: Maintains a clear audit trail for every transaction, including creator and approver details[cite: 1].

### 3. User Experience & Interface
* **Single Page Application (SPA)**: Powered by Inertia.js for seamless, lightning-fast navigation without full-page reloads.
* **Hybrid Layout**: Seamlessly integrates a robust admin panel for internal management and a clean, responsive landing page for general access.
* **Dark Mode Support**: Includes a persistent theme switcher that adapts UI elements and assets (like logos) based on user preference.

## 🛠️ Tech Stack

* **Backend**: Laravel 13, Filament v5 (Schema-based Architecture).
* **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS, Flowbite.
* **Routing**: Ziggy Vue for shared Laravel routes in JavaScript.
* **Containerization**: Docker with Supervisor management.

## ⚙️ Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/FyrnDly/erp-finance.git
cd erp-finance
```

### 2. Environment Setup
```bash
cp .env.example .env
# Adjust your DB_HOST and other credentials in .env
```

---

## 🐳 Option 1: Running with Docker (Recommended)

This method uses the provided **Docker Compose** setup which isolates the environment and manages both the web server and queue workers automatically via **Supervisor**.

1. **Build and Start Containers**
   ```bash
   docker compose up --build
   ```
   *The container will automatically run `php artisan storage:link`, `ziggy:generate`, and cache your configurations upon startup.*

2. **Run Migrations & Seed (First time only)**
   ```bash
   docker compose exec app php artisan migrate --seed
   ```

3. **Access the Application**
   * URL: `http://localhost:8000`
   * *The internal container runs on port 80, managed by Supervisor.*

---

## 💻 Option 2: Local Development (Without Docker)

Use this method if you prefer running the application directly on your host machine.

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Generate Key & Migrate**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

3. **Configure Composer Script**
   Ensure your `composer.json` includes the following script for a unified dev experience:
   ```json
   "scripts": {
       "dev": [
           "php artisan ziggy:generate",
           "npx concurrently \"php artisan serve\" \"npm run dev\" --names=\"server,vite\" --prefix-colors=\"blue,magenta\""
       ]
   }
   ```

4. **Run Application**
   ```bash
   composer run dev
   ```

---

## 🔐 Access Control

The system utilizes Laravel Model Policies to strictly enforce roles:
* **Manager**: Can configure the Chart of Accounts, manage all journal entries, and perform final verification on all operational documents.
* **Staff**: Can create and manage their own submissions (Invoices/Expenses) while awaiting managerial approval.
