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

    public function postStat(Request $request)
    {
        abort_if($request->members === null || $request->fleetNote === null
            || $request->fleetType === null || $request->PAP === null, 403);
        abort_if(!$this->checkPostRight($request->fleetType), 403);
        $members = explode("\n", $request->members);
        $job = new ImportFleetStat($members, $this->getMainCharacter(auth()->user()->group_id)->name, $request->PAP, $request->fleetType, $request->fleetNote);
        dispatch($job)->onQueue('high');
        return back()->withInput();
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
