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
        // 1. Routings & Operations
        Schema::create('routings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('routing_code', 50)->unique();
            $table->string('name');
            $table->string('version', 20)->default('v1.0');
            $table->enum('status', ['draft', 'active', 'archived'])->default('active');
            $table->integer('total_cycle_time_mins')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('routing_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routing_id')->constrained('routings')->cascadeOnDelete();
            $table->integer('sequence_no')->default(10);
            $table->string('operation_name');
            $table->string('work_center');
            $table->integer('setup_time_mins')->default(15);
            $table->integer('run_time_mins')->default(30);
            $table->decimal('hourly_cost', 10, 2)->default(350.00);
            $table->boolean('requires_qc')->default(false);
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        // 2. Enhance BOM Headers
        Schema::table('bom_headers', function (Blueprint $table) {
            $table->string('approval_status', 30)->default('approved')->after('status');
            $table->date('effective_date')->nullable()->after('approval_status');
            $table->date('expiry_date')->nullable()->after('effective_date');
            $table->text('change_reason')->nullable()->after('expiry_date');
            $table->foreignId('routing_id')->nullable()->after('product_id')->constrained('routings')->nullOnDelete();
            $table->decimal('material_cost', 15, 2)->default(0.00)->after('standard_cost');
            $table->decimal('labour_cost', 15, 2)->default(0.00)->after('material_cost');
            $table->decimal('overhead_cost', 15, 2)->default(0.00)->after('labour_cost');
        });

        // 3. Enhance BOM Components
        Schema::table('bom_components', function (Blueprint $table) {
            $table->string('component_type', 30)->default('raw_material')->after('product_id');
            $table->decimal('subtotal_cost', 15, 2)->default(0.00)->after('scrap_percentage');
            $table->integer('operation_seq')->default(10)->after('subtotal_cost');
            $table->string('issue_warehouse', 100)->nullable()->after('operation_seq');
            $table->string('remarks')->nullable()->after('issue_warehouse');
        });

        // 4. Enhance Work Orders
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('priority', 20)->default('medium')->after('status');
            $table->string('sales_order_ref', 50)->nullable()->after('priority');
            $table->string('supervisor', 100)->nullable()->after('sales_order_ref');
            $table->string('production_line', 50)->default('Line 1')->after('work_center');
            $table->foreignId('routing_id')->nullable()->after('bom_header_id')->constrained('routings')->nullOnDelete();
            $table->string('material_status', 30)->default('available')->after('production_line');
            $table->text('notes')->nullable()->after('target_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['routing_id']);
            $table->dropColumn(['priority', 'sales_order_ref', 'supervisor', 'production_line', 'routing_id', 'material_status', 'notes']);
        });

        Schema::table('bom_components', function (Blueprint $table) {
            $table->dropColumn(['component_type', 'subtotal_cost', 'operation_seq', 'issue_warehouse', 'remarks']);
        });

        Schema::table('bom_headers', function (Blueprint $table) {
            $table->dropForeign(['routing_id']);
            $table->dropColumn(['approval_status', 'effective_date', 'expiry_date', 'change_reason', 'routing_id', 'material_cost', 'labour_cost', 'overhead_cost']);
        });

        Schema::dropIfExists('routing_operations');
        Schema::dropIfExists('routings');
    }
};
