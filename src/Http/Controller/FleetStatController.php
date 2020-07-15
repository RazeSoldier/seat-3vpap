<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Http\Request;
use RazeSoldier\Seat3VPap\Jobs\ImportFleetStat;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Services\Models\UserSetting;

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
        $job = new ImportFleetStat($members, $this->getMainCharacter(auth()->user()->group_id)->name, $request->point, $request->notice);
        dispatch($job)->onQueue('high');
        return response()->json(['status' => 'ok']);
    }

    private function getMainCharacter($gid) :? CharacterInfo
    {
        $userSetting = UserSetting::where([
            'group_id' => $gid,
            'name' => 'main_character_id'
        ])->first();
        if ($userSetting === null) {
            return null;
        }
        $uid = $userSetting->value;
        if ($uid === null) {
            return null;
        }
        return CharacterInfo::find($uid);
    }
}
