<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use JsonException;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\Activitylog\Models\Activity;

class ActivitiesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query(): Builder
    {
        return Activity::query()
            ->select([
                'id',
                'log_name',
                'description',
                'event',
                'causer_id',
                'causer_type',
                'properties',
                'created_at',
                'subject_type',
            ]);
        // Note: We're not using with('causer') here because we'll handle the relationship manually
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Event',
            'Causer',
            'Properties',
            'Created At',
            'Type',
        ];
    }

    /**
     * @param  Activity  $activity
     *
     * @throws JsonException
     */
    public function map($activity): array
    {
        // Manually resolve the causer name based on causer_type and causer_id
        $causerName = 'N/A';

        if ($activity->causer_id && $activity->causer_type) {
            // Check if causer is a User
            if ($activity->causer_type === 'App\\Models\\User') {
                $user = User::find($activity->causer_id);
                $causerName = $user->name ?? "User #$activity->causer_id";
            } else {
                // For other model types, show the type and ID
                $modelType = class_basename($activity->causer_type);
                $causerName = "$modelType #$activity->causer_id";
            }
        }

        // Convert properties to string if needed
        $properties = $activity->properties;
        if (is_object($properties) || is_array($properties)) {
            $properties = json_encode($properties, JSON_THROW_ON_ERROR);
        }

        return [
            $activity->id,
            $activity->log_name ?? 'N/A',
            $activity->description ?? 'N/A',
            $activity->event ?? 'N/A',
            $causerName,
            $properties ?? 'N/A',
            $activity->created_at ? $activity->created_at->format('d M Y H:i:s') : 'N/A',
            $activity->subject_type ? class_basename($activity->subject_type) : 'None',
        ];
    }
}
