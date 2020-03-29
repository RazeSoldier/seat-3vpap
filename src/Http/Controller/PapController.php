<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Database\Eloquent\Collection;
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
                foreach ($corp->characters as $character) {
                    // Filter out not-main character
                    if (!$this->isMainCharacter($character->character_id)) {
                        continue;
                    }

                    $group = User::find($character->character_id)->group;
                    $users = $group->users->all();
                    $corp->point = 0;
                    foreach ($users as $user) {
                        $corp->point += Pap::where('characterName', $user->name)->sum('PAP');
                    }
                }
            }
            $corpList = $corpList->all();
            usort($corpList, function ($a, $b) {
                if ($a->point === $b->point) {
                    return 0;
                }
                return ($a->point > $b->point) ? -1 : 1;
            });
        } else {
            $corpList = [];
        }
        return view('pap::page', [
            'isAdmin' => auth()->user()->has('pap.admin', false),
            'linkedTotalPap' => $this->getLinkedTotalPap(),
            'linkedMonthPap' => $this->getLinkedMonthPap(),
            'totalPap' => Pap::where('characterName', auth()->user()->name)->sum('PAP'),
            'monthPap' => Pap::where([
                ['characterName', auth()->user()->name],
                ['fleetTime', '>', date('Y-m-d 00:00:00')]
            ])->sum('PAP'),
            'gid' => auth()->user()->group_id,
            'corpList' => $corpList,
            'pingCount' => Pap::select('fleetTime')->distinct()->get()->count(),
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
            'title' => $gid === auth()->user()->group_id ? __('pap::pap.myPap') : $this->getMainCharacter($gid)->name . __('pap::pap.pap-title-suffix'),
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
            $point = 0;
            $linkedCount = 0;
            foreach ($users as $user) {
                $point += Pap::where('characterName', $user->name)->sum('PAP');
                ++$linkedCount;
            }
            $memberList[] = [
                'name' => $character->name,
                'pap' => $point,
                'groupId' => $group->id,
                'linkedCount' => $linkedCount,
            ];
        }
        usort($memberList, function ($a, $b) {
            if ($a['pap'] === $b['pap']) {
                return 0;
            }
            return ($a['pap'] > $b['pap']) ? -1 : 1;
        });
        return view('pap::corp', [
            'title' => $corp->name . __('pap::pap.pap-title-suffix'),
            'memberList' => $memberList,
        ]);
    }

    private function getLinkedTotalPap() : int
    {
        $users = $this->getLinkedUsers();
        $point = 0;
        foreach ($users as $user) {
            $point += Pap::where('characterName', $user->name)->sum('PAP');
        }
        return $point;
    }

    private function getLinkedMonthPap() : int
    {
        $users = $this->getLinkedUsers();
        $point = 0;
        foreach ($users as $user) {
            $point += Pap::where([
                ['characterName', $user->name],
                ['fleetTime', '>', date('Y-m-d 00:00:00')]
            ])->sum('PAP');
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
