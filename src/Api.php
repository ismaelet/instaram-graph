<?php

namespace Ismaelet\InstagramGraph;

use \DB;
use \HttpResponse;

class Api {

	private const DEBUGGING = false;

	public static function refreshToken($oldToken) {
		$endpoint = 'https://graph.instagram.com/refresh_access_token';
		$queryFields = [
			'grant_type' => 'ig_refresh_token',
			'access_token' => $oldToken
		];

		$response = new HttpResponse('GET', $endpoint, $queryFields);
		$response = $response->json();

		if (self::DEBUGGING) debug($response);

		if (!isset($response['error'])) {
			$newToken = $response['access_token'];

			DB::update('config SET value=? WHERE name="instagramToken"', [$newToken]);
			DB::update('config SET value=NOW() WHERE name="instagramTokenTime"');

			return $newToken;
		} else {
			error_log('Instagram API error on token refresh: ' . print_r($response['error'], true));

			return null;
		}
	}
}
