<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('badges:sync')->daily();
