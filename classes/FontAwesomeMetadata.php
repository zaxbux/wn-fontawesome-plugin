<?php namespace Zaxbux\FontAwesome\Classes;

use Cache;
use Config;

class FontAwesomeMetadata {
	use \System\Traits\ConfigMaker;

	const CACHE_KEY = 'fontawesome_metadata';

	const STYLE_SOLID   = 'solid';
	const STYLE_REGULAR = 'regular';
	const STYLE_LIGHT   = 'light';
	const STYLE_DUOTONE = 'duotone';
	const STYLE_BRANDS  = 'brands';

	const STYLE_CLASS = [
		'solid'   => 'fas',
		'regular' => 'far',
		'light'   => 'fal',
		'duotone' => 'fad',
		'brands'  => 'fab',
	];

	const CONFIG_ICONS      = '/metadata/icons.yml';
	const CONFIG_CATEGORIES = '/metadata/categories.yml';

	private $configPathIcons;
	private $configPathCategories;

	public function __construct($basePath = null) {
		if (!$basePath) {
			$package = Config::get('zaxbux.fontawesome::fontawesome.pro', false) ? 'pro' : 'free';
			$basePath = Config::get('zaxbux.fontawesome::fontawesome.packages')[$package];
		}

		$this->configPathIcons      = $basePath . self::CONFIG_ICONS;
		$this->configPathCategories = $basePath . self::CONFIG_CATEGORIES;
	}

	public function getIconMetadata() {
		return Cache::rememberForever(self::CACHE_KEY, function() {
			$icons = $this->makeConfig($this->configPathIcons);
			$categories = $this->makeConfig($this->configPathCategories);

			foreach ($icons as $iconID => $icon) {
				// ignore private icons
				if (array_get($icon, 'private') == true) {
					unset($icons->{$iconID});
					continue;
				}

				// brand icons category
				if (\in_array(self::STYLE_BRANDS, $icon['styles'])) {
					$icons->{$iconID}['categories'] = ['Brands'];
					continue;
				}

				$icons->{$iconID}['categories'] = [];
				
				foreach ($categories as $categoryID => $category) {
					if (\in_array($iconID, $category['icons'])) {
						$icons->{$iconID}['categories'][] = $category['label'];
					}
				}

				// Catch-all category
				if (!isset($icons->{$iconID}['categories'])) $icons->{$iconID}['categories'] = ['Other'];
			}

			return $icons;
		});
	}

	public function resultsByCategory($query, $style) {
		$query = \strtolower($query);

		$categories = [];

		foreach ($this->getIconMetadata() as $id => $icon) {
			// Check if any of the provided styles match the icon
			if (!\in_array($style, $icon['styles'])) continue;

			// Search the icon's metadata
			if (!empty($query) && !self::searchIconTerms($query, $id, $icon['label'], ...$icon['search']['terms'])) continue;

			foreach ($icon['categories'] as $category) {
				if (!isset($categories[$category])) $categories[$category] = [];

				$categories[$category][] = [
					'id'   => $id,
					'text' => $icon['label'],
					'icon' => self::getIconStyleClass($id, $style),
				];
			}
		}

		$results = [];

		foreach ($categories as $label => $icons) {
			$results[] = [
				'text' => $label,
				'children' => $icons,
			];
		}
		
		return $results;
	}

	/**
	 * Query a list of terms.
	 * 
	 * @param string $query
	 * @param string $terms,...
	 * @return bool
	 */
	protected static function searchIconTerms(string $query, string ...$terms) {
		foreach ($terms as $term) {
			return \strpos(\strtolower($term), $query) !== false;
		}

		return false;
	}

	/**
	 * Returns a formatted class string for displaying the icon.
	 * 
	 * @param string $icon
	 * @param string $style
	 * @return string
	 */
	public static function getIconStyleClass(string $icon, string $style = self::STYLE_REGULAR) {
		return \sprintf('%s fa-%s', self::STYLE_CLASS[$style], $icon);
	}

	public function iconExists($icon) {
		return isset($this->getIconMetadata()->{$icon});
	}
}