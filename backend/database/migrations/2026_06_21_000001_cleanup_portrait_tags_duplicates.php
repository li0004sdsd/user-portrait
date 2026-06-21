<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('portrait_tags')
            ->select('user_portrait_id', 'tag_id', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('user_portrait_id', 'tag_id')
            ->having('cnt', '>', 1)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $deleteIds = [];

            foreach ($duplicates as $dup) {
                $extras = DB::table('portrait_tags')
                    ->where('user_portrait_id', $dup->user_portrait_id)
                    ->where('tag_id', $dup->tag_id)
                    ->where('id', '>', $dup->keep_id)
                    ->pluck('id');

                foreach ($extras as $id) {
                    $deleteIds[] = $id;
                }
            }

            if (!empty($deleteIds)) {
                DB::table('portrait_tags')
                    ->whereIn('id', $deleteIds)
                    ->delete();
            }
        }

        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('portrait_tags');
        $hasUnique = false;

        foreach ($indexes as $index) {
            $columns = $index->getColumns();
            sort($columns);
            if ($index->isUnique() && $columns === ['tag_id', 'user_portrait_id']) {
                $hasUnique = true;
                break;
            }
        }

        if (!$hasUnique) {
            Schema::table('portrait_tags', function (Blueprint $table) {
                $table->unique(['user_portrait_id', 'tag_id']);
            });
        }
    }

    public function down(): void
    {
    }
};
