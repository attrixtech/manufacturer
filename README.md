# WebotApp-Apex-Manufacturing-ERP

> **Enterprise-Grade Manufacturing Execution System (MES) & ERP**  
> Built with Laravel 11/12, Livewire, Tailwind CSS, and MySQL.

---

## 🏭 Overview

**WebotApp Apex Manufacturing ERP** is a full-featured, commercially viable Manufacturing ERP & Shop Floor Execution System designed for discrete, batch, and continuous process manufacturing operations. Built with multi-company and multi-plant capabilities, it brings real-time shop floor telemetry, perpetual double-entry inventory ledger (FIFO), multi-level BOM versioning, QA/QC quarantine workflows, and role-based access control.

---

## 🚀 Key Architectural Features

- **Role-Adaptive Operational Dashboards**: Tailored workspaces for Super Admin, Plant Managers, Production Heads, Quality Inspectors, Inventory Managers, Finance Controllers, and Shop Floor Machine Operators.
- **Multi-Company & Multi-Plant Topology**: Unlimited companies, factories, warehouses, and departments with seamless workspace switching.
- **Perpetual Inventory Ledger**: Immutable, double-entry stock transactions with batch/lot and serial number genealogy.
- **Quality Control (QA/QC)**: Quarantine bays, Incoming QC (IQC), In-Process QC (IPQC), Final QC (FQC), and Non-Conformance Reports (NCR/CAPA).
- **Manufacturing Execution (MES)**: Digital Job Cards, live machine telemetry (spindle load, coolant pressure, tool life), shift output vs. targets, and downtime logging.
- **Enterprise Security**: Spatie RBAC with 9 pre-configured manufacturing roles and tamper-evident activity/audit logging.

---

## 🛠️ Technology Stack

| Component | Technology |
|---|---|
| **Backend Framework** | Laravel 11/12 (PHP 8.4+) |
| **Interactive UI** | Livewire 4.x + Alpine.js |
| **Styling** | Tailwind CSS 4.x |
| **Database** | MySQL 8.x (Strict Foreign Keys & Decimal Precision) |
| **Authentication & RBAC** | Laravel Auth + Spatie Laravel-Permission |
| **Testing** | PHPUnit Feature & Integration Test Suite |

---

## 📦 Quick Installation

### 1. Prerequisites
- PHP 8.3+ with `bcmath`, `curl`, `mbstring`, `pdo_mysql`, `zip`
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.x

### 2. Setup
```bash
# Clone the repository
git clone https://github.com/webotapp-academy/WebotApp-Apex-Manufacturing-ERP.git
cd WebotApp-Apex-Manufacturing-ERP

# Install PHP & Node dependencies
composer install
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Configure database in .env and run migrations with seeders
php artisan migrate --seed

# Build frontend assets
npm run build

# Start local server
php artisan serve
```

---

## 🔑 Demo Access Credentials

| Role | Email | Password | Assigned Unit |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@apexerp.com` | `password` | Mumbai Corporate HQ |
| **Plant Manager** | `plant.manager@apexerp.com` | `password` | Pune Heavy Fab Plant |
| **Production Head** | `production@apexerp.com` | `password` | Pune Heavy Fab Plant |
| **Quality Inspector** | `quality@apexerp.com` | `password` | Pune Heavy Fab Plant |
| **Inventory Manager** | `inventory@apexerp.com` | `password` | Pune Heavy Fab Plant |
| **Accounts Manager** | `accounts@apexerp.com` | `password` | Mumbai Corporate HQ |
| **Machine Operator** | `operator@apexerp.com` | `password` | Pune CNC Milling Line |

---

## 🧪 Testing

Run automated feature tests:
```bash
php artisan test
```

---

## 📄 License
Commercial / Proprietary & Open Evaluation. Developed by [WebotApp Academy](https://github.com/webotapp-academy).
