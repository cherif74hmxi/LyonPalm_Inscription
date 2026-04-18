<?php

use Illuminate\Support\Facades\Schedule;

// Placeholder schedule configuration for future cron tasks.
Schedule::command('queue:prune-batches --hours=48')->daily();
