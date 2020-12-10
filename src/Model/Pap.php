<?php

namespace RazeSoldier\Seat3VPap\Model;

use Illuminate\Database\Eloquent\{
    Collection,
    Model,
    Relations\BelongsTo
};
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

/**
 * @property string characterName
 * @property CharacterInfo|null character
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
     * @param string $name
     * @return int PAP
     */
    public static function getCharacterPap(string $name) : int
    {
        return self::where([
            ['characterName', $name],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
        ])->get()->sum('PAP');
    }

    /**
     * Get the corporation's PAP for the last 30 days
     *
     * @param int $corpId
     * @return Collection PAP
     */
    public static function getCorporationPapById(int $corpId) : Collection
    {
        $corpName = CorporationInfo::find($corpId)->name;
        return self::where([
            ['corpName', $corpName],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')]
        ])->get();
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
