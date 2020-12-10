<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Web\Http\Controllers\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ApiCorpController extends Controller
{
	/**
	 * Returns JSON data that PAP in the last 30 days
	 * @Route("/pap/api/corp/{id}", name="pap.get-corppap", methods={"GET"})
	 * @param int $id The corporation id
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function getCorpMemberPap(int $id): JsonResponse
    {
        try {
            $corp = CorporationInfo::findOrFail($id); // Checks corporation exists
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => "Missing corporation id: $id",
            ]);
        }

        // Fetch all character PAP data in the last 30 days of this corporation from DB
        $res = Pap::where([
            ['corpName', $corp->name],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
        ])->get();
        $resp = [];
        /** @var Pap $pap */
        foreach ($res as $pap) {
            // We display data in group. Each user group will have a main character.
            if ($pap->character === null) {
                // If the model cannot be found in character_infos table,
                // it is assumed that the character is not registered for SeAT.
                if (!isset($resp[$pap->characterName])) {
                    $resp[$pap->characterName] = self::initGroupPapArray();
                    $resp[$pap->characterName]['noesi'] = true;
                    $resp[$pap->characterName]['characterName'] = $pap->characterName;
                }
                self::sumPap($resp[$pap->characterName], $pap->PAP);
            } else {
                $user = $pap->character->user;
                $mc = $user->main_character;
                if ($mc === null) {
                    // Means cannot find main character from user_settings,
                    // so default is the first character in the group.
                    $mc = $user->characters->first->get(); // Fallback main character
                }
                if (!isset($resp[$mc->name])) {
                    $resp[$mc->name] = self::initGroupPapArray();
                    $resp[$mc->name]['characterName'] = $mc->name;
                    $resp[$mc->name]['groupId'] = $user->id;
                    $resp[$mc->name]['characterLinkCount'] = $user->characters->count();
                }
                self::sumPap($resp[$mc->name], $pap->PAP);
            }
        }
        return response()->json(array_values($resp));
    }

    private static function initGroupPapArray() : array
    {
        return [
            'point' => 0,
            'characterLinkCount' => 1,
            'noesi' => false,
        ];
    }

    private static function sumPap(array &$data, int $point)
    {
        $data['point'] += $point;
    }

    private static function getLast30Days() : \DateTime
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        return $date->sub(new \DateInterval('P30D'));
    }
}