<?php

namespace RazeSoldier\Seat3VPap;

trait Helper
{
	private function checkPermission($targetUid): bool
	{
		$user = auth()->user();
		return $targetUid === $user->id || $user->can('pap.admin');
	}
}