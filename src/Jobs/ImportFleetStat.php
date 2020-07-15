<?php

namespace RazeSoldier\Seat3VPap\Jobs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Jobs\Character\Info;
use Seat\Services\Repositories\Corporation\Corporation;

class ImportFleetStat extends Info
{
    use Corporation;

    private $members;
    private $fcName;
    private $pap;
    private $fleetNote;
    public $tries = 4;

    public function __construct(array $members, string $fcName, int $pap, string $fleetNote)
    {
        $this->members = $members;
        $this->fcName = $fcName;
        $this->pap = $pap;
        $this->fleetNote = $fleetNote;
        parent::__construct();
    }

    public function handle()
    {
        while (!$this->preflighted()) {
            sleep(60);
        }

        DB::transaction(function () {
            $time = (new \DateTime('now', new \DateTimeZone('Asia/Shanghai')))
                ->format('Y-m-d H:i:s');
            foreach ($this->members as $member) {
                $client = $this->eseye()->setVersion('v2')->setQueryString([
                    'categories' => 'character',
                    'strict' => 'true',
                    'search' => $member,
                ]);
                $uid = $client->invoke('get', '/search')['character'][0];
                $characterInfo = $this->retrieve([
                    'character_id' => $uid,
                ]);

                $papStore = new Pap();
                $papStore->characterName = $characterInfo->name;
                $papStore->corpName = Cache::remember("corp-{$characterInfo['corporation_id']}", 5, function() use ($characterInfo) {
                    $client = $this->eseye()->setVersion('v4');
                    $corpInfo = $client->invoke('get', "/corporations/{$characterInfo['corporation_id']}");
                    return $corpInfo->name;
                });
                if (isset($characterInfo['alliance_id'])) {
                    $papStore->allianceName = Cache::remember("alliance-{$characterInfo['alliance_id']}", 5, function () use ($characterInfo) {
                        $client = $this->eseye()->setVersion('v3');
                        $allianceInfo = $client->invoke('get', "/alliances/{$characterInfo['alliance_id']}");
                        return $allianceInfo['ticker'];
                    });
                }
                $papStore->PAP = $this->pap;
                $papStore->fleetFC = $this->fcName;
                $papStore->fleetTime = $time;
                $papStore->fleetNote = $this->fleetNote;
                $papStore->save();
            }
        });
    }
}
