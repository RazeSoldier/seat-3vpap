<?php

namespace RazeSoldier\Seat3VPap\Jobs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RazeSoldier\Seat3VPap\Model\Pap;
use Seat\Eveapi\Jobs\EsiBase;

class ImportFleetStat extends EsiBase
{
	protected $tags = ['import_fleet'];
	protected $method = 'get';
	protected $endpoint = '/characters/{character_id}/';
	protected $version = 'v4';

	/**
	 * @var array
	 */
	private $members;

	/**
	 * @var string
	 */
	private $fcName;

	/**
	 * @var int
	 */
	private $pap;

	/**
	 * @var string
	 */
	private $fleetNote;

	public function __construct(array $members, string $fcName, int $pap, string $fleetNote)
	{
		$this->members = $members;
		$this->fcName = $fcName;
		$this->pap = $pap;
		$this->fleetNote = $fleetNote;
	}

	public function handle()
	{
		DB::transaction(function () {
			$time = (new \DateTime('now', new \DateTimeZone('Asia/Shanghai')))
				->format('Y-m-d H:i:s');
			foreach ($this->members as $member) {
				$characterInfo = $this->retrieve([
					'character_id' => $this->queryCharacterIdFromName($member),
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

	/**
	 * @param string $name
	 * @return int
	 * @throws \Seat\Eseye\Exceptions\EsiScopeAccessDeniedException
	 * @throws \Seat\Eseye\Exceptions\InvalidAuthenticationException
	 * @throws \Seat\Eseye\Exceptions\InvalidContainerDataException
	 * @throws \Seat\Eseye\Exceptions\RequestFailedException
	 * @throws \Seat\Eseye\Exceptions\UriDataMissingException
	 */
	private function queryCharacterIdFromName(string $name): int
	{
		$client = $this->eseye()->setVersion('v2')->setQueryString([
			'categories' => 'character',
			'strict' => 'true',
			'search' => $name,
		]);
		return $client->invoke('get', '/search')['character'][0];
	}
}
