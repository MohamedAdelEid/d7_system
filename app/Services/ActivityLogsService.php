<?php

namespace App\Services;

use App\Models\Activity_log;
use Illuminate\Support\Facades\Hash;

class ActivityLogsService
{
    public function insert($data){
        Activity_log::create([
            'subject_id'    => $data['subject']->id,  // Ensure this is the subject's ID
            'subject_type'  => get_class($data['subject']),  // Store the model class as subject_type
            'description'   => $data['description'] ?? null,
            'title'         => $data['title'],
            'proccess_type' => $data['proccess_type'] ?? null,
            'user_id'       => $data['user_id'],
            'properties'    => $data['properties'] ?? [],
        ]);
    }
}