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
            $aPoint = 0;
            $bPoint = 0;
            $cPoint = 0;
            foreach ($corp->characters as $character) {
                // Filter out not-main character
                if (!$this->isMainCharacter($character->character_id)) {
                    continue;
                }

                $group = User::find($character->character_id)->group;
                $users = $group->users->all();
                foreach ($users as $user) {
                    $aPoint += Pap::where([
                        ['characterName', $user->name],
                        ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
                        ['fleetType', 'A'],
                    ])->sum('PAP');
                    $bPoint += Pap::where([
                        ['characterName', $user->name],
                        ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
                        ['fleetType', 'B'],
                    ])->sum('PAP');
                    $cPoint += Pap::where([
                        ['characterName', $user->name],
                        ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
                        ['fleetType', 'C'],
                    ])->sum('PAP');
                }
            }
            Cache::forever("pap::corp-{$corp->corporation_id}-a", $aPoint);
            Cache::forever("pap::corp-{$corp->corporation_id}-b", $bPoint);
            Cache::forever("pap::corp-{$corp->corporation_id}-c", $cPoint);
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

    private static function getLast30Days() : \DateTime
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        return $date->sub(new \DateInterval('P30D'));
    }
}
