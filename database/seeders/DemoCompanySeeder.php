<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoCompanySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo Manufacturing Company
        $company = Company::firstOrCreate(
            ['code' => 'APEX-01'],
            [
                'name' => 'Apex Precision Engineering Pvt. Ltd.',
                'legal_name' => 'Apex Precision Engineering Private Limited',
                'gstin' => '27AABCA1234F1Z5',
                'pan' => 'AABCA1234F',
                'cin' => 'U28100MH2020PTC345678',
                'email' => 'operations@apexprecision.in',
                'phone' => '+91 20 6712 3400',
                'website' => 'https://apexprecision.in',
                'currency' => 'INR',
                'currency_symbol' => '₹',
                'address' => 'Plot No. C-14, Chakan MIDC Phase II',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'state_code' => '27',
                'country' => 'India',
                'postal_code' => '410501',
                'is_active' => true,
                'settings' => [
                    'fiscal_year_start_month' => 4, // April for India
                    'valuation_method' => 'fifo',
                    'allow_negative_stock' => false,
                    'auto_post_journals' => true,
                ],
            ]
        );

        // 2. Create Manufacturing Plants / Branches
        $punePlant = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'PUNE-PLT'],
            [
                'name' => 'Pune Heavy Fabrication & Assembly Plant',
                'type' => 'factory',
                'gstin' => '27AABCA1234F1Z5',
                'phone' => '+91 20 6712 3401',
                'email' => 'plant.pune@apexprecision.in',
                'address' => 'Plot No. C-14, Chakan MIDC Phase II',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'state_code' => '27',
                'country' => 'India',
                'postal_code' => '410501',
                'is_head_office' => false,
                'is_active' => true,
            ]
        );

        $blrPlant = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'BLR-PLT'],
            [
                'name' => 'Bengaluru CNC Machining Unit',
                'type' => 'factory',
                'gstin' => '29AABCA1234F1Z3',
                'phone' => '+91 80 4123 9800',
                'email' => 'plant.blr@apexprecision.in',
                'address' => 'Plot 48, Peenya Industrial Area 3rd Phase',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'state_code' => '29',
                'country' => 'India',
                'postal_code' => '560058',
                'is_head_office' => false,
                'is_active' => true,
            ]
        );

        $mumbaiHQ = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'BOM-HQ'],
            [
                'name' => 'Mumbai Corporate Headquarters',
                'type' => 'head_office',
                'gstin' => '27AABCA1234F1Z5',
                'phone' => '+91 22 2845 0000',
                'email' => 'hq@apexprecision.in',
                'address' => 'Express Towers, 14th Floor, Nariman Point',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'state_code' => '27',
                'country' => 'India',
                'postal_code' => '400021',
                'is_head_office' => true,
                'is_active' => true,
            ]
        );

        // 3. Create Core Manufacturing Departments
        $deptList = [
            ['code' => 'ENG', 'name' => 'Engineering & R&D', 'desc' => 'BOM, CAD, and Process Routing Engineering'],
            ['code' => 'PROD', 'name' => 'Production & Assembly', 'desc' => 'Shop Floor Execution, Work Orders, and Line Operations'],
            ['code' => 'QAQC', 'name' => 'Quality Assurance & QC', 'desc' => 'Incoming, In-Process, and Final Quality Control'],
            ['code' => 'SCM', 'name' => 'Supply Chain & Inventory', 'desc' => 'Warehouse, Stock Ledger, and Material Movements'],
            ['code' => 'PROC', 'name' => 'Procurement & Purchase', 'desc' => 'Vendor Management, RFQs, POs, and Vendor Invoices'],
            ['code' => 'MAINT', 'name' => 'Plant Maintenance', 'desc' => 'Preventive, Breakdown, and Spare Parts Management'],
            ['code' => 'SALES', 'name' => 'Sales & Customer Logistics', 'desc' => 'Quotations, Sales Orders, Challans, and Invoicing'],
            ['code' => 'FIN', 'name' => 'Finance & Cost Accounting', 'desc' => 'General Ledger, AP/AR, Cost Centers, and Statutory GST'],
        ];

        $departments = [];
        foreach ($deptList as $d) {
            $departments[$d['code']] = Department::firstOrCreate(
                ['company_id' => $company->id, 'code' => $d['code']],
                [
                    'branch_id' => $punePlant->id,
                    'name' => $d['name'],
                    'description' => $d['desc'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Create Users with Assigned Roles
        $usersData = [
            [
                'name' => 'Rajesh Sharma',
                'email' => 'admin@apexerp.com',
                'role' => 'Super Administrator',
                'dept' => 'ENG',
                'branch' => $mumbaiHQ->id,
                'designation' => 'Chief Technology & Systems Officer',
                'employee_code' => 'EMP-001',
            ],
            [
                'name' => 'Vikram Deshmukh',
                'email' => 'plant.manager@apexerp.com',
                'role' => 'Plant Manager',
                'dept' => 'PROD',
                'branch' => $punePlant->id,
                'designation' => 'General Manager - Operations',
                'employee_code' => 'EMP-002',
            ],
            [
                'name' => 'Anil Kulkarni',
                'email' => 'production@apexerp.com',
                'role' => 'Production Head',
                'dept' => 'PROD',
                'branch' => $punePlant->id,
                'designation' => 'Senior Production Manager',
                'employee_code' => 'EMP-003',
            ],
            [
                'name' => 'Pooja Nair',
                'email' => 'quality@apexerp.com',
                'role' => 'Quality Inspector',
                'dept' => 'QAQC',
                'branch' => $punePlant->id,
                'designation' => 'QA/QC Lead Engineer',
                'employee_code' => 'EMP-004',
            ],
            [
                'name' => 'Suresh Patil',
                'email' => 'inventory@apexerp.com',
                'role' => 'Warehouse & Inventory Manager',
                'dept' => 'SCM',
                'branch' => $punePlant->id,
                'designation' => 'Stores & Logistics In-Charge',
                'employee_code' => 'EMP-005',
            ],
            [
                'name' => 'Meera Sundaram',
                'email' => 'accounts@apexerp.com',
                'role' => 'Accounts & Finance Manager',
                'dept' => 'FIN',
                'branch' => $mumbaiHQ->id,
                'designation' => 'Financial Controller',
                'employee_code' => 'EMP-006',
            ],
            [
                'name' => 'Dinesh Pawar',
                'email' => 'operator@apexerp.com',
                'role' => 'Machine Operator',
                'dept' => 'PROD',
                'branch' => $punePlant->id,
                'designation' => 'Senior CNC Milling Operator',
                'employee_code' => 'EMP-007',
            ],
        ];

        foreach ($usersData as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'company_id' => $company->id,
                    'branch_id' => $u['branch'],
                    'department_id' => $departments[$u['dept']]->id ?? null,
                    'name' => $u['name'],
                    'phone' => '+91 98200 ' . rand(10000, 99999),
                    'designation' => $u['designation'],
                    'employee_code' => $u['employee_code'],
                    'status' => 'active',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role
            $user->syncRoles([$u['role']]);

            // Assign branch access
            $user->branches()->syncWithoutDetaching([
                $punePlant->id => ['is_default' => $u['branch'] === $punePlant->id],
                $blrPlant->id => ['is_default' => $u['branch'] === $blrPlant->id],
                $mumbaiHQ->id => ['is_default' => $u['branch'] === $mumbaiHQ->id],
            ]);
        }
    }
}

