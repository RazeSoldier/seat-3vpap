<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use RazeSoldier\Seat3VPap\Helper;
use PhpOffice\PhpSpreadsheet\{
    Spreadsheet,
    Writer\Xls
};
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Models\{
    Character\CharacterInfo,
    Corporation\CorporationInfo
};
use Seat\Services\Models\UserSetting;
use Seat\Web\Http\Controllers\Controller;
use Seat\Web\Models\User;
use Symfony\Component\Routing\Annotation\Route;

class PapController extends Controller
{
	use Helper;

	/**
	 * @Route("/pap", name="pap.home", methods={"GET"})
	 */
    public function showMainPage()
    {
        $isAdmin = auth()->user()->can('pap.admin');

        if ($isAdmin) {
            $corpList = CorporationInfo::all();
            /** @var CorporationInfo $corp */
            foreach ($corpList as $corp) {
                $corp->point = Cache::get("pap::corp-{$corp->corporation_id}-pap");
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
            'isAdmin' => $isAdmin,
            'point' => $this->getUserPap(),
            'uid' => auth()->user()->id,
            'corpList' => $corpList,
            'ctaCount' => Pap::getCTACount(),
        ]);
    }

	/**
	 * @Route("/pap/group/{user_id}", name="pap.pap", methods={"GET"})
	 */
    public function showGroupPap(int $userId)
    {
        // Non-admin cannot access other people's PAP record
	    $user = auth()->user();
	    if (!$this->checkPermission($userId)) {
            abort(404);
        }
        User::findOrFail($userId); // Checks user exists

	    /** @var Collection|Pap[] $paps */
	    $paps = [];
        $user->characters->each(function (CharacterInfo $character) use (&$paps) {
        	$paps = array_merge($paps, Pap::where('characterName', $character->name)->get()->toArray());
        });

        return view('pap::pap', [
            'title' => $userId === $user->id ? __('pap::pap.myPap-title') : $this->getMainCharacter($user)->name . __('pap::pap.pap-title-suffix'),
            'groupId' => $userId,
        ]);
    }

    /**
     * Route: /pap/corporation/{id}
     */
    public function showCorporation(int $id)
    {
        $corp = CorporationInfo::findOrFail($id); // Checks corporation exists

        return view('pap::corp', [
            'title' => $corp->name . __('pap::pap.pap-title-suffix'),
            'corpId' => $id,
        ]);
    }

    /**
     * Route: /pap/download/corp/{id}
     */
    public function downloadCorpPapExecl($id)
    {
        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        // Set headers
        $sheet->setCellValue('A1', 'characterName');
        $sheet->setCellValue('B1', 'corpName');
        $sheet->setCellValue('C1', 'PAP');
        $sheet->setCellValue('D1', 'fleetFC');
        $sheet->setCellValue('E1', 'fleetTime');
        $sheet->setCellValue('F1', 'fleetNote');

        $paps = Pap::getCorporationPapById($id);
        $i = 1;
        foreach ($paps as $pap) {
            $i++;
            $sheet->setCellValue("A$i", $pap->characterName);
            $sheet->setCellValue("B$i", $pap->corpName);
            $sheet->setCellValue("C$i", $pap->PAP);
            $sheet->setCellValue("D$i", $pap->fleetFC);
            $sheet->setCellValue("E$i", $pap->fleetTime);
            $sheet->setCellValue("F$i", $pap->fleetNote);
        }

        $writer = new Xls($ss);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'seat-3vpap');
        $writer->save($tempFilePath);
        return response()->download($tempFilePath, "pap-corp-$id.xls");
    }

    private function getUserPap(): int
    {
	    $point = 0;
    	auth()->user()->characters->each(function (CharacterInfo $character) use (&$point) {
		    $point += Pap::getCharacterPap($character->name);
	    });
    	return $point;
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
