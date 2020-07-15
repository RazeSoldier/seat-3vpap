<?php

namespace RazeSoldier\Seat3VPap\Model;

use Illuminate\Database\Eloquent\{
    Model,
    Relations\BelongsTo
};
use Seat\Eveapi\Models\Character\CharacterInfo;

/**
 * @property string characterName
 * @property BelongsTo|null character
 * @property string fleetType
 * @property int PAP
 */
class Pap extends Model
{
    protected $table = 'PAPs';
    public $timestamps = false;

    public function character() :? BelongsTo
    {
        return $this->belongsTo(CharacterInfo::class, 'characterName', 'name');
    }

    /**
     * Get the character's PAP for the last 30 days
     *
     * @param CharacterInfo $characterInfo
     * @return int PAP
     */
    public static function getCharacterPap(CharacterInfo $characterInfo) : int
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
        ])->get()->sum('PAP');
    }

    /**
     * Get the CTA count for the last 30 days
     * @return int
     */
    public static function getCTACount() : int
    {
        return self::select('fleetTime')->where(
            'fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')
        )->distinct()->get()->count();
    }

    private static function getLast30Days() : \DateTime
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        return $date->sub(new \DateInterval('P30D'));
    }
}
