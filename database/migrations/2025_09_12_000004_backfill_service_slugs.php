<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        // no-op
    }
};


