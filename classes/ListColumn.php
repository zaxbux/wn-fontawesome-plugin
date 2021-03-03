<?php

namespace Zaxbux\FontAwesome\Classes;

class ListColumn {
	/**
	 * @return array
	 */
	public static function registration() : array {
		return [
			'fontawesome' => [__CLASS__, 'fontawesome'],
		];
	}

	/**
	 * @return string
	 */
	public static function fontawesome($value, $column, $record) : string {
		if ($iconField = $column->getConfig('fontawesome.iconField')) {
			return \sprintf('<i class="%s fa-fw"></i>&nbsp;%s', FontAwesomeMetadata::getIconStyleClass($record->{$iconField}, $column->getConfig('style', FontAwesomeMetadata::STYLE_REGULAR)), $value);
		}

		return \sprintf('<i class="%s fa-fw"></i>&nbsp;%s', FontAwesomeMetadata::getIconStyleClass($value, $column->getConfig('style', FontAwesomeMetadata::STYLE_REGULAR)), $value);
	}
}