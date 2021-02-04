<?php

namespace Zaxbux\FontAwesome\Validation;

use Lang;
use Illuminate\Contracts\Validation\Rule;
use Zaxbux\FontAwesome\Classes\FontAwesomeMetadata;

class FontAwesome implements Rule {
	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value) {
		$metadata = new FontAwesomeMetadata;

		return $metadata->iconExists($value);
	}

	/**
	 * Validation callback method.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @param  array  $params
	 * @return bool
	 */
	public function validate($attribute, $value, $params)
	{
		return $this->passes($attribute, $value);
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message() {
		return Lang::get('zaxbux.fontawesome::validation.fontawesome');
	}
}