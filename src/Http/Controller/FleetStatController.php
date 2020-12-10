<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Http\Request;
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
        $job = new ImportFleetStat($members, auth()->user()->main_character->name, $request->point, $request->notice);
        dispatch($job)->onQueue('high');
        return response()->json(['status' => 'ok']);
    }
}
