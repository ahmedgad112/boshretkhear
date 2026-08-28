<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function requirePermission(string $permission): void
    {
        abort_unless(
            auth()->user()?->can($permission),
            403,
            'غير مسموح لك بتنفيذ هذه العملية.'
        );
    }
}
