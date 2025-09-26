<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;

class SurveyPolicy
{
    public function view(User $user, Survey $survey): bool
    {
        return $survey->user_id === $user->id;
    }

    public function update(User $user, Survey $survey): bool
    {
        return $survey->user_id === $user->id && $survey->isEditable();
    }

    public function delete(User $user, Survey $survey): bool
    {
        return $survey->user_id === $user->id;
    }

    public function export(User $user, Survey $survey): bool
    {
        return $survey->user_id === $user->id;
    }
}

