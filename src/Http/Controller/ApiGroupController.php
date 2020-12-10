<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use RazeSoldier\Seat3VPap\Helper;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Web\Http\Controllers\Controller;
use Seat\Web\Models\User;
use Symfony\Component\Routing\Annotation\Route;

class ApiGroupController extends Controller
{
	use Helper;

    /**
     * @Route("/pap/api/group/{id}", name="pap.api-group", methods={"GET"})
     */
    public function getGroupPap(int $userId)
    {
        // Non-admin cannot access other people's PAP record
        if (!$this->checkPermission($userId)) {
            abort(404);
        }
        $user = User::findOrFail($userId); // Checks user exists

        $paps = [];
        $user->characters->each(function (CharacterInfo $character) use (&$paps) {
	        $paps = array_merge($paps, Pap::where('characterName', $character->name)->get()->toArray());
        });
        return $paps;
    }
}
