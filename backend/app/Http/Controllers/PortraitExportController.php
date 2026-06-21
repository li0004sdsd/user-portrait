<?php

namespace App\Http\Controllers;

use App\Models\ExportLog;
use App\Models\UserPortrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortraitExportController extends Controller
{
    public function export(Request $request): StreamedResponse|\Illuminate\Http\JsonResponse
    {
        $userId = $request->user()->id;
        $lockKey = "portrait_export:{$userId}";

        $lock = Cache::lock($lockKey, 300);

        if (!$lock->get()) {
            return response()->json([
                'message' => '导出任务已在进行中，请稍候'
            ], 429);
        }

        try {
            $rowCount = 0;

            return response()->stream(function () use ($userId, &$rowCount) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    '画像ID',
                    '姓名',
                    '年龄',
                    '性别',
                    '职业',
                    '地区',
                    '收入水平',
                    '兴趣爱好',
                    '痛点',
                    '目标',
                    '状态',
                    '标签分类',
                    '标签名称',
                    '标签值',
                    '标签权重',
                    '创建时间',
                ]);

                $query = UserPortrait::with('tags.category')
                    ->where('user_id', $userId)
                    ->latest();

                foreach ($query->cursor() as $portrait) {
                    $baseRow = [
                        $portrait->id,
                        $portrait->name,
                        $portrait->age,
                        $portrait->gender,
                        $portrait->occupation,
                        $portrait->location,
                        $portrait->income_level,
                        $portrait->interests,
                        $portrait->pain_points,
                        $portrait->goals,
                        $portrait->status,
                        '',
                        '',
                        '',
                        '',
                        $portrait->created_at->toDateTimeString(),
                    ];

                    if ($portrait->tags->isEmpty()) {
                        fputcsv($handle, $baseRow);
                        $rowCount++;
                    } else {
                        foreach ($portrait->tags as $tag) {
                            $row = $baseRow;
                            $row[11] = $tag->category?->name ?? '';
                            $row[12] = $tag->name;
                            $row[13] = $tag->value;
                            $row[14] = $tag->weight;
                            fputcsv($handle, $row);
                            $rowCount++;
                        }
                    }
                }

                fclose($handle);

                ExportLog::create([
                    'user_id' => $userId,
                    'row_count' => $rowCount,
                ]);
            }, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="portraits_' . date('Ymd_His') . '.csv"',
            ]);
        } finally {
            $lock->release();
        }
    }
}
