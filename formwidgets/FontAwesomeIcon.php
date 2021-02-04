<?php

namespace Zaxbux\FontAwesome\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Zaxbux\FontAwesome\Classes\FontAwesomeMetadata;
use Zaxbux\FontAwesome\Behaviors\FontAwesomeIconAssets;

class FontAwesomeIcon extends FormWidgetBase {
	/**
	 * @var int default minimum input length to start searching
	 */
	public const DEFAULT_MIN_INPUT_LENGTH = 1;

	/**
	 * @var int default delay before request of search results with AJAX
	 */
	public const DEFAULT_AJAX_DELAY = 300; 
	
	public $style = FontAwesomeMetadata::STYLE_REGULAR;

	protected $defaultAlias = 'fontawesome';

	/**
	 * {@inheritDoc}
	 */
	public function init(): void {
		$this->fillFromConfig([
			'style',
			'fontAwesomePath'
		]);
	}

	public function render() {
		$this->prepareVars();
		
		return $this->makePartial('default');
	}

	/**
	 * {@inheritDoc}
	 */
	protected function loadAssets() {
		if (!$this->controller->isClassExtendedWith(FontAwesomeIconAssets::class)) {
			$this->extendClassWith(FontAwesomeIconAssets::class);
		}
	}

	public function prepareVars(): void {
		if ($assetBehavior = $this->controller->getClassExtension(FontAwesomeIconAssets::class)) {
			$this->fontAwesomePath = $assetBehavior->getFontAwesomePath();
		}

		$this->formField->type = 'dropdown';
		$this->formField->options = $this->getInitialValueOption($this->formField->value, $this->style) ?? [];
		$this->formField->attributes['field']['data-handler'] = 'onFontawesomeIconDropdownSearch';
		$this->formField->attributes['field']['data-request-data'] = "_style: '{$this->style}'";
		$this->formField->attributes['field']['data-minimum-input-length'] = $this->formField->attributes['field']['data-minimum-input-length'] ?? self::DEFAULT_MIN_INPUT_LENGTH;
		$this->formField->attributes['field']['data-ajax--delay'] = $this->formField->attributes['field']['data-ajax--delay'] ?? self::DEFAULT_AJAX_DELAY;

		$this->vars['field'] = $this->formField;
	}

	/**
	 * Return a list of icons in Select2 format
	 * See: https://select2.org/data-sources/formats
	 */
	public function onFontawesomeIconDropdownSearch(): array {
		$searchQuery  = post('q');
		$style        = post('_style', $this->style);
		$page         = intVal(post('page', 1)); // page parameter for load-more requests
		
		$results = [];

		if ($this->formField->getConfig('emptyOption') && $page === 1) {
			$results[] = [ 'id' => '', 'text' => $this->formField->getConfig('emptyOption') ];
		}

		$fa = new FontAwesomeMetadata($this->getFontAwesomePath());

		$results += $fa->resultsByCategory($searchQuery, $style);

		return ['results' => $results, 'pagination' => ['more' => false]];
	}

	/**
	 * Get basic icon config for an initial value.
	 * 
	 * @param string|null $icon
	 * @param string $style
	 * @return array
	 */
	public function getInitialValueOption(?string  $icon, string $style = null): ?array {
		if (!$icon) return null;

		$fa = new FontAwesomeMetadata($this->getFontAwesomePath());

		foreach ($fa->getIconMetadata() as $id => $iconData) {
			if ($id == $icon) 
				return [ $id => [ $iconData['label'], FontAwesomeMetadata::getIconStyleClass($id, $style) ] ];
		}
	}

	protected function getFontAwesomePath(): ?string {
		return $this->getConfig('fontAwesomePath', $this->fontAwesomePath);
	}
}