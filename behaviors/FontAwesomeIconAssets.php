<?php

namespace Zaxbux\FontAwesome\Behaviors;

use Backend\Classes\ControllerBehavior;
use Zaxbux\FontAwesome\Classes\FontAwesomeMetadata;

/**
 * Injects the JavaScript necessary for displaying Font Awesome icons.
 */
class FontAwesomeIconAssets extends ControllerBehavior {

	public const FORM_WIDGET_CODE = 'fontawesome';
	public const LIST_COLUMN_CODE = 'fontawesome';

	/**
	 * @var string Default base path for Font Awesome NPM package
	 */
	public const DEFAULT_PATH = '$/zaxbux/fontawesome/node_modules/@fortawesome/fontawesome-pro';

	/**
	 * @var array Default asset mapping
	 */
	public const DEFAULT_ASSETS = [
		'all'         => '/js/all.min.js',
		'brands'      => '/js/brands.min.js',
		'regular'     => '/js/regular.min.js',
		'solid'       => '/js/solid.min.js',
		'light'       => '/js/light.min.js',
		'duotone'     => '/js/duotone.min.js',
		'fontawesome' => '/js/fontawesome.min.js',
	];

	/**
	 * @var array Default styles to load
	 */
	public const DEFAULT_STYLES = [
		'regular',
	];

	/**
	 * @var array Default config for JS API
	 */
	public const DEFAULT_API_CONFIG = [
		'autoReplaceSvg' => 'nest',
	];

	public $fontAwesomeConfig = [];

	/**
	 * @var array List of auto-discovered icon styles used for asset injection.
	 */
	protected $iconStyles = [];

	/**
	 * Behavior constructor
	 * @param \Backend\Classes\Controller $controller
	 */
	public function __construct($controller) {
		parent::__construct($controller);

		if (is_array($controller->fontAwesomeConfig)) {
			$this->fontAwesomeConfig = $controller->fontAwesomeConfig;
		}

		$this->setConfig($this->fontAwesomeConfig);

		$this->loadAssets();
	}
	
	/**
	 * Called after the list columns are defined.
	 * @param \Backend\Widgets\Lists $host The hosting list widget
	 * @return void
	 */
	public function listExtendColumns($host) {
		if (!$this->getConfig('autodiscover', true)) return;

		foreach ($host->getColumns() as $column) {
			if ($column->type == self::LIST_COLUMN_CODE) {
				$this->addIconStyle($column->getConfig('style', FontAwesomeMetadata::STYLE_REGULAR));
			}
		}
	}

	/**
	 * Called after the form fields are defined.
	 * @param \Backend\Widgets\Form $host The hosting form widget
	 * @return void
	 */
	public function formExtendFieldsBefore($host) {
		if (!$this->getConfig('autodiscover', true)) return;

		$host->bindEvent('form.extendFields', function () use ($host) {
			foreach ($host->getFields() as $field) {
				if ($field->type == self::FORM_WIDGET_CODE) {
					$this->addIconStyle($field->getConfig('style'));
				}
			}
		});
	}

	public function getFontAwesomePath() {
		return $this->getConfig('path', self::DEFAULT_PATH);
	}

	/**
	 * Update the internal list of icon styles
	 */
	protected function addIconStyle(string $style) {
		\array_merge($this->iconStyles, [$style]);
	}

	/**
	 * Convert JS API configuration values to HTML data-* attributes.
	 * E.g. `autoReplaceSvg` -> `data-auto-replace-svg`
	 * 
	 * @return array
	 */
	protected function getDataAttributes(): array {
		$config = $this->getConfig('apiConfig', self::DEFAULT_API_CONFIG);

		$attributes = [];
		
		foreach ($config as $key => $value) {
			$attributes['data-'.kebab_case($key)] = $value;
		}

		return $attributes;
	}

	/**
	 * Inject Font Awesome assets
	 */
	protected function loadAssets(): void {
		$styles = array_merge(['fontawesome'], $this->iconStyles, $this->getConfig('styles', self::DEFAULT_STYLES));

		if (count($styles) == 1) return;

		$assets = [];

		$path = $this->getFontAwesomePath();
		foreach ($styles as $style) {
			$assets[] = $path . array_get($this->getConfig('assets', self::DEFAULT_ASSETS), $style);
		}

		$this->addJs($assets, $this->getDataAttributes());
	}
}