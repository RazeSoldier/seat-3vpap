<?php

namespace RazeSoldier\Seat3VPap\Jobs;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Services\Models\UserSetting;
use Seat\Web\Models\User;

class UpdateCorpPap extends Command
{
    protected $signature = 'pap:update';
    protected $description = 'Update corporation PAP every hour';

    public function handle()
    {
        $corpList = CorporationInfo::all();
        /** @var CorporationInfo $corp */
        foreach ($corpList as $corp) {
            $totalPoint = 0;
            foreach ($corp->characters as $character) {
                // Filter out not-main character
                if (!$this->isMainCharacter($character->character_id)) {
                    continue;
                }

                $group = User::find($character->character_id)->group;
                $users = $group->users->all();
                foreach ($users as $user) {
                    $totalPoint += Pap::where([
                        ['characterName', $user->name],
                        ['fleetTime', '>', date('Y-m-01 00:00:00')]
                    ])->sum('PAP');
                }
            }
            Cache::forever("pap::corp-{$corp->corporation_id}", $totalPoint);
        }
    }

    private function isMainCharacter($uid) : bool
    {
        $userSetting = UserSetting::where([
            'group_id' => User::find($uid)->group_id,
            'name' => 'main_character_id'
        ])->first();
        if ($userSetting === null) {
            return false;
        }
        $mainId = $userSetting->value;
        return $uid == $mainId;
    }
}
