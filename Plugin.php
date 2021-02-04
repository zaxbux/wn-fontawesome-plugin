<?php namespace Zaxbux\FontAwesome;

use Lang;
use Validator;

use System\Classes\PluginBase;
use Zaxbux\FontAwesome\Classes\ListColumn;
//use Zaxbux\FontAwesome\Classes\TwigExtension;

class Plugin extends PluginBase {

	/*public function registerMarkupTags() {
		return [
			'filters'   => TwigExtension::registerFilters(),
			'functions' => TwigExtension::registerFunctions(),
		];
	}*/

	public function registerListColumnTypes() {
		return ListColumn::registration();
	}

	public function registerFormWidgets() {
		return [
			\Zaxbux\FontAwesome\FormWidgets\FontAwesomeIcon::class => [
				'code' => 'fontawesome',
				'label' => 'zaxbux.fontawesome::lang.formWidgets.fontawesome',
			],
		];
	}

	public function boot() {
		$this->registerValidators();
	}

	private function registerValidators() {
		Validator::extend('fontawesome', \Zaxbux\FontAwesome\Validation\FontAwesomeIcon::class);
	}
}
