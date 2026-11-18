<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ProjectStatusF;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProjectStatusRecorder
{
    public function createUnlessConsecutive(array $attributes, string $statusModel = ProjectStatus::class): ?Model
    {
        if (!in_array($statusModel, [ProjectStatus::class, ProjectStatusF::class], true)) {
            throw new InvalidArgumentException('Unsupported project status model.');
        }

        if (!isset($attributes['project_id'], $attributes['stage_id'])) {
            throw new InvalidArgumentException('A project and stage are required to record a project status.');
        }

        return DB::transaction(function () use ($attributes, $statusModel) {
            Project::whereKey($attributes['project_id'])->lockForUpdate()->firstOrFail();

            $latestStatus = ProjectStatus::where('project_id', $attributes['project_id'])
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($latestStatus && (int) $latestStatus->stage_id === (int) $attributes['stage_id']) {
                return null;
            }

            $status = new $statusModel();
            $status->fill($attributes);
            $status->save();

            return $status;
        });
    }
}
