<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Products Master
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->enum('type', ['raw_material', 'semi_finished', 'finished_good', 'consumable'])->default('raw_material');
            $table->string('category')->default('General');
            $table->string('uom', 20)->default('Pcs'); // Unit of measure: Kg, Pcs, Liters, Meters
            $table->decimal('standard_cost', 15, 2)->default(0.00);
            $table->decimal('current_stock', 15, 2)->default(0.00);
            $table->decimal('reorder_level', 15, 2)->default(10.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Warehouses
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->enum('type', ['raw_store', 'wip_floor', 'finished_store', 'quarantine'])->default('raw_store');
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        // 3. BOM Headers & Components
        Schema::create('bom_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('bom_number', 50)->unique();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('version', 20)->default('REV-01');
            $table->decimal('output_qty', 15, 2)->default(1.00);
            $table->decimal('standard_cost', 15, 2)->default(0.00);
            $table->enum('status', ['draft', 'approved', 'obsolete'])->default('approved');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bom_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_header_id')->constrained('bom_headers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 15, 4)->default(1.0000);
            $table->string('uom', 20)->default('Pcs');
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->decimal('scrap_percentage', 5, 2)->default(0.00);
            $table->timestamps();
        });

        // 4. Work Orders (MES)
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('wo_number', 50)->unique();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('bom_header_id')->nullable()->constrained('bom_headers')->nullOnDelete();
            $table->decimal('planned_qty', 15, 2);
            $table->decimal('completed_qty', 15, 2)->default(0.00);
            $table->decimal('scrap_qty', 15, 2)->default(0.00);
            $table->enum('status', ['draft', 'released', 'in_progress', 'completed', 'cancelled'])->default('released');
            $table->string('work_center')->default('Line 1');
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->timestamps();
        });

        // 5. Perpetual Stock Ledger
        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('batch_no', 50)->nullable();
            $table->decimal('quantity_change', 15, 2); // Positive for receipt, negative for issue
            $table->decimal('balance_after', 15, 2);
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->decimal('total_value', 15, 2)->default(0.00);
            $table->string('voucher_type', 50); // GRN, Production Issue, Transfer, Adjustment
            $table->string('source_doc', 50);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 6. Purchase Orders (Procurement)
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('po_number', 50)->unique();
            $table->string('supplier_name');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->enum('status', ['draft', 'approved', 'received_partial', 'completed', 'cancelled'])->default('approved');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('received_qty', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 7. Quality Inspections & NCRs
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('inspection_number', 50)->unique();
            $table->enum('type', ['incoming_iqc', 'in_process_ipqc', 'final_fqc'])->default('incoming_iqc');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('batch_no', 50)->nullable();
            $table->string('reference_doc', 50)->nullable();
            $table->decimal('sample_size', 15, 2)->default(10.00);
            $table->decimal('accepted_qty', 15, 2)->default(0.00);
            $table->decimal('rejected_qty', 15, 2)->default(0.00);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'quarantined'])->default('pending');
            $table->text('findings')->nullable();
            $table->timestamps();
        });

        Schema::create('non_conformance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('ncr_number', 50)->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['minor', 'major', 'critical'])->default('major');
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->string('disposition')->nullable();
            $table->timestamps();
        });

        // 8. Machines & Maintenance
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('work_center')->default('Line 1');
            $table->enum('status', ['running', 'idle', 'maintenance', 'breakdown'])->default('running');
            $table->integer('spindle_load')->default(72);
            $table->decimal('coolant_pressure', 5, 2)->default(4.80);
            $table->integer('tool_life')->default(85);
            $table->timestamps();
        });

        Schema::create('machine_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            $table->string('incident_number', 50)->unique();
            $table->string('reported_by')->nullable();
            $table->text('issue_description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->integer('downtime_minutes')->default(0);
            $table->enum('status', ['reported', 'under_repair', 'resolved'])->default('reported');
            $table->timestamps();
        });

        // 9. General Ledger & Chart of Accounts
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('entry_number', 50)->unique();
            $table->date('entry_date');
            $table->string('reference_doc', 50)->nullable();
            $table->text('description');
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
        Schema::dropIfExists('machine_breakdowns');
        Schema::dropIfExists('machines');
        Schema::dropIfExists('non_conformance_reports');
        Schema::dropIfExists('quality_inspections');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_ledger');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('bom_components');
        Schema::dropIfExists('bom_headers');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('products');
    }
};
