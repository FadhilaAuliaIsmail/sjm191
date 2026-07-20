<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_produksi', function (Blueprint $table) {
            $table->string('nama_barang')->after('produk_id');
            $table->enum('sumber', ['pasar', 'sponsor'])->after('nama_barang');
            $table->integer('jumlah_dibeli')->after('sumber');
            $table->decimal('berat_per_satuan', 10, 2)->after('jumlah_dibeli');
            $table->string('satuan', 20)->after('berat_per_satuan');
            $table->decimal('harga_beli', 12, 2)->nullable()->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('barang_produksi', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'sumber', 'jumlah_dibeli', 'berat_per_satuan', 'satuan', 'harga_beli']);
        });
    }
};
