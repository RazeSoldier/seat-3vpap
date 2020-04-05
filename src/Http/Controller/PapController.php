<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Models\{
    Character\CharacterInfo,
    Corporation\CorporationInfo
};
use Seat\Services\Models\UserSetting;
use Seat\Web\Http\Controllers\Controller;
use Seat\Web\Models\{
    Group,
    User
};

class PapController extends Controller
{
    public function showMainPage()
    {
        $isAdmin = auth()->user()->has('pap.admin', false);
        if ($isAdmin) {
            $corpList = CorporationInfo::all();
            /** @var CorporationInfo $corp */
            foreach ($corpList as $corp) {
                $corp->aPoint = Cache::get("pap::corp-{$corp->corporation_id}-a");
                $corp->bPoint = Cache::get("pap::corp-{$corp->corporation_id}-b");
                $corp->cPoint = Cache::get("pap::corp-{$corp->corporation_id}-c");
            }
            $corpList = $corpList->all();
            usort($corpList, function ($a, $b) {
                if ($a->aPoint === $b->aPoint) {
                    return 0;
                }
                return ($a->aPoint > $b->aPoint) ? -1 : 1;
            });
        } else {
            $corpList = [];
        }
        return view('pap::page', [
            'isAdmin' => auth()->user()->has('pap.admin', false),
            'aPap' => $this->getLinkedMonthAPoint(),
            'bPap' => $this->getLinkedMonthBPoint(),
            'cPap' => $this->getLinkedMonthCPoint(),
            'gid' => auth()->user()->group_id,
            'corpList' => $corpList,
            'aPing' => Pap::getMonthAPingCount(),
            'bPing' => Pap::getMonthBPingCount(),
            'cPing' => Pap::getMonthCPingCount(),
        ]);
    }

    public function showGroupPap(int $gid)
    {
        // Non-admin cannot access other people's PAP record
        if ($gid !== auth()->user()->group_id && !auth()->user()->has('pap.admin', false)) {
            abort(404);
        }
        Group::findOrFail($gid); // Checks group exists
        $users = Group::find($gid)->users()->getResults()->all();
        /** @var Collection[] $paps */
        $paps = [];
        foreach ($users as $user) {
            $paps = array_merge($paps, Pap::where('characterName', $user->name)->get()->all());
        }
        return view('pap::pap', [
            'title' => $gid === auth()->user()->group_id ? __('pap::pap.myPap-title') : $this->getMainCharacter($gid)->name . __('pap::pap.pap-title-suffix'),
            'fleets' => $paps,
        ]);
    }

    public function showCorporation(int $id)
    {
        // Non-admin cannot access corporation's PAP record
        if (!auth()->user()->has('pap.admin', false)) {
            abort(404);
        }

        $corp = CorporationInfo::findOrFail($id); // Checks corporation exists

        $memberList = [];
        /** @var CharacterInfo $character */
        foreach ($corp->characters as $character) {
            // Filter out not-main character
            if (!$this->isMainCharacter($character->character_id)) {
                continue;
            }

            $group = User::find($character->character_id)->group;
            $users = $group->users->all();
            $aPoint = 0;
            $bPoint = 0;
            $cPoint = 0;
            $linkedCount = 0;
            foreach ($users as $user) {
                $aPoint += Pap::getCharacterMonthAPoint($user->character);
                $bPoint += Pap::getCharacterMonthBPoint($user->character);
                $cPoint += Pap::getCharacterMonthCPoint($user->character);
                ++$linkedCount;
            }
            $memberList[] = [
                'name' => $character->name,
                'aPoint' => $aPoint,
                'bPoint' => $bPoint,
                'cPoint' => $cPoint,
                'groupId' => $group->id,
                'linkedCount' => $linkedCount,
            ];
        }
        usort($memberList, function ($a, $b) {
            if ($a['aPoint'] === $b['aPoint']) {
                return 0;
            }
            return ($a['aPoint'] > $b['aPoint']) ? -1 : 1;
        });
        return view('pap::corp', [
            'title' => $corp->name . __('pap::pap.pap-title-suffix'),
            'memberList' => $memberList,
        ]);
    }

    private function getLinkedMonthAPoint() : int
    {
        $users = $this->getLinkedUsers();
        $point = 0;
        foreach ($users as $user) {
            $point += Pap::getCharacterMonthAPoint(CharacterInfo::find($user->id));
        }
        return $point;
    }

    private function getLinkedMonthBPoint() : int
    {
        $users = $this->getLinkedUsers();
        $point = 0;
        foreach ($users as $user) {
            $point += Pap::getCharacterMonthBPoint(CharacterInfo::find($user->id));
        }
        return $point;
    }

    private function getLinkedMonthCPoint() : int
    {
        $users = $this->getLinkedUsers();
        $point = 0;
        foreach ($users as $user) {
            $point += Pap::getCharacterMonthCPoint(CharacterInfo::find($user->id));
        }
        return $point;
    }

    /**
     * @return User[]
     */
    private function getLinkedUsers() : array
    {
        return auth()->user()->group()->getResults()->users()->getResults()->all();
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
