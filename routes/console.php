<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('badges:sync')->daily();
Schedule::command('download-records:prune')->daily();
Schedule::command('sitemap:generate')->daily();
