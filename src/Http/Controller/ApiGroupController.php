<?php

namespace RazeSoldier\Seat3VPap\Http\Controller;

use Illuminate\Database\Eloquent\Collection;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Web\Http\Controllers\Controller;
use Seat\Web\Models\Group;

class ApiGroupController extends Controller
{
    /**
     * Route: /api/group/{id}
     */
    public function getGroupPap(int $groupId)
    {
        // Non-admin cannot access other people's PAP record
        if ($groupId !== auth()->user()->group_id && !auth()->user()->has('pap.admin', false)) {
            abort(404);
        }
        Group::findOrFail($groupId); // Checks group exists
        $users = Group::find($groupId)->users()->getResults()->all();
        /** @var Collection[] $paps */
        $paps = [];
        foreach ($users as $user) {
            $paps = array_merge($paps, Pap::where('characterName', $user->name)->get()->all());
        }
        return $paps;
    }
}
