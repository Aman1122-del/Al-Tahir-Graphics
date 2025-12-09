<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Only backfill if slug column exists and is nullable
        if (!Schema::hasColumn('services', 'slug')) {
            return;
        }

        $services = DB::table('services')->select('id', 'title', 'slug')->get();
        $used = DB::table('services')->whereNotNull('slug')->pluck('slug')->toArray();

        foreach ($services as $row) {
            if (!empty($row->slug)) { continue; }
            $base = Str::slug($row->title) ?: 'service';
            $slug = $base;
            $suffix = 2;
            while (in_array($slug, $used, true)) {
                $slug = $base . '-' . $suffix;
                $suffix++;
            }
            $used[] = $slug;
            DB::table('services')->where('id', $row->id)->update(['slug' => $slug]);
        }

        // After backfilling, make slug NOT NULL and add unique constraint
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        // no-op
    }
};


