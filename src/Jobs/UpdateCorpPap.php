<?php

namespace RazeSoldier\Seat3VPap\Jobs;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

/**
 * This is a maintenance script that used to refresh the PAP point of corporation.
 * @package RazeSoldier\Seat3VPap\Jobs
 */
class UpdateCorpPap extends Command
{
    protected $signature = 'pap:update';
    protected $description = 'Update corporation PAP (last 30 days)';

    public function handle()
    {
        $corpList = CorporationInfo::all();
        /** @var CorporationInfo $corp */
        foreach ($corpList as $corp) {
            $point = Pap::where([
                ['corpName', $corp->name],
                ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
            ])->sum('PAP');
            Cache::forever("pap::corp-{$corp->corporation_id}-pap", $point);
        }
    }

    private static function getLast30Days() : \DateTime
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        return $date->sub(new \DateInterval('P30D'));
    }
}
