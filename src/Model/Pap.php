<?php

namespace RazeSoldier\Seat3VPap\Model;

use Illuminate\Database\Eloquent\{Collection, Model, Relations\BelongsTo};
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

    public static function getCharacterPap(CharacterInfo $characterInfo) : Collection
    {
        return self::where('characterName', $characterInfo->name)->get();
    }

    public static function getCharacterMonthPap(CharacterInfo $characterInfo) : Collection
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
        ])->get();
    }

    public static function getCharacterAPoint(CharacterInfo $characterInfo) : int
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetType', 'A'],
        ])->get()->sum('PAP');
    }

    public static function getCharacterMonthAPoint(CharacterInfo $characterInfo) : int
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetType', 'A'],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
        ])->get()->sum('PAP');
    }

    public static function getCharacterBPoint(CharacterInfo $characterInfo) : int
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetType', 'B'],
        ])->get()->sum('PAP');
    }

    public static function getCharacterMonthBPoint(CharacterInfo $characterInfo) : int
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetType', 'B'],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
        ])->get()->sum('PAP');
    }

    public static function getCharacterCPoint(CharacterInfo $characterInfo) : int
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetType', 'C'],
        ])->get()->sum('PAP');
    }

    public static function getCharacterMonthCPoint(CharacterInfo $characterInfo) : int
    {
        return self::where([
            ['characterName', $characterInfo->name],
            ['fleetType', 'C'],
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
        ])->get()->sum('PAP');
    }

    public static function getMonthAPingCount() : int
    {
        return self::select('fleetTime')->where([
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
            ['fleetType', 'A'],
        ])->distinct()->get()->count();
    }

    public static function getMonthBPingCount() : int
    {
        return self::select('fleetTime')->where([
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
            ['fleetType', 'B'],
        ])->distinct()->get()->count();
    }

    public static function getMonthCPingCount() : int
    {
        return self::select('fleetTime')->where([
            ['fleetTime', '>', self::getLast30Days()->format('Y-m-d H:i:s')],
            ['fleetType', 'C'],
        ])->distinct()->get()->count();
    }

    private static function getLast30Days() : \DateTime
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        return $date->sub(new \DateInterval('P30D'));
    }
}
