<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RazeSoldier\Seat3VPap\Jobs\ImportFleetStat;

class FleetStatController
{
    /**
     * Route: /pap/fleet-stat
     */
    public function showHome()
    {
        return view('pap::stat-home');
    }

    /**
     * Route: /pap/api/post-stat
     */
    public function postStat(Request $request)
    {
        abort_if($request->text === null || $request->notice === null
            || $request->point === null, 403);
        $members = explode("\n", $request->text);
        $this->saveFile($request);
        $job = new ImportFleetStat($members, auth()->user()->main_character->name, $request->point, $request->notice);
        dispatch($job)->onQueue('high');
        return response()->json(['status' => 'ok']);
    }

    /**
     * Save the fleet stat to a file
     * @param Request $request
     */
    private function saveFile(Request $request)
    {
        $filename = 'fleetstat-' . Carbon::now()->format('YmdHis') . '-' . $request->notice . '.txt';
        $time = Carbon::now()->addHours(8)->format('Y-m-d H:i:s');
        $username = auth()->user()->main_character->name;

        $text = <<<TEXT
Time: $time
FC: $username
Point: $request->point
Notice: $request->notice
Member:
$request->text
TEXT;

        Storage::disk('local')->put("fleetstat/$filename", $text);
    }
}
