<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Http\Request;
use RazeSoldier\Seat3VPap\Jobs\ImportFleetStat;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Services\Models\UserSetting;

class FleetStatController
{
    public function showHome()
    {
        return view('pap::stat-home', [
            'fcRight' => $this->getFCRight(),
        ]);
    }

    private function getFCRight() :? string
    {
        if (auth()->user()->has('pap.aFC', false)) {
            return 'A';
        }
        if (auth()->user()->has('pap.bFC', false)) {
            return 'B';
        }
        if (auth()->user()->has('pap.cFC', false)) {
            return 'C';
        }
        return null;
    }

    /**
     * Route: /pap/api/post-stat
     */
    public function postStat(Request $request)
    {
        abort_if($request->text === null || $request->notice === null
            || $request->type === null || $request->point === null, 403);
        abort_if(!$this->checkPostRight($request->type), 403);
        $members = explode("\n", $request->text);
        $job = new ImportFleetStat($members, $this->getMainCharacter(auth()->user()->group_id)->name, $request->point, $request->type, $request->notice);
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

    private function checkPostRight(string $fleetType) : bool
    {
        switch ($fleetType) {
            case 'A':
                if ($this->getFCRight() === 'A') {
                    return true;
                }
                break;
            case 'B':
                if ($this->getFCRight() === 'A' || $this->getFCRight() === 'B') {
                    return true;
                }
                break;
            case 'C':
                if ($this->getFCRight() === 'A' || $this->getFCRight() === 'B' || $this->getFCRight() === 'C') {
                    return true;
                }
        }
        return false;
    }
}
