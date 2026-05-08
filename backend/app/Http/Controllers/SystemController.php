<?php

namespace App\Http\Controllers;

use App\Services\System\ChangelogService;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    protected ChangelogService $changelogService;

    public function __construct(ChangelogService $changelogService)
    {
        $this->changelogService = $changelogService;
    }

    /**
     * Display the application changelog
     */
    public function changelog()
    {
        $entries = $this->changelogService->getParsedChangelog();

        return view('system.changelog', [
            'entries' => $entries
        ]);
    }
}
