<?php
class DispletReader_Woopa_View
{
	protected $_config;
	protected $_contents;
	protected $_model;

	// @todo changed from private to protected, send upstream
	protected static function _render($path, &$model, $model_alias = false) {
		if (file_exists($path)) {
			if ($model_alias) {
				$model_alias = (string) $model_alias;
				$$model_alias = &$model;
			}

			ob_start();

			include $path;

			$output = ob_get_contents();

			ob_end_clean();
			return trim($output);
		}		
	}

	/**
	 * Get a world-facing template
	 *
	 * Returns the rendered contents as a string. $name is without path, path
	 * is found via global config object.
	 *
	 * @param $name Filename of template.
	 * @param &$model Model that will be scoped into template.
	 * @param $model_alias (Optional) An alternative name for the model.
	 * @return string
	 */
	public static function get_template($name, &$model, $model_alias = false) {
		$config = DispletReader_Woopa_Registry::get('config');
		$path = $config->template_dir . DIRECTORY_SEPARATOR . $name;
		return self::_render($path, $model, $model_alias);
	}

	/**
	 * Get a backend-facing template
	 *
	 * Returns the rendered contents as a string. $name is without path, path
	 * is found via global config object.
	 *
	 * @param $name Filename of template.
	 * @param &$model Model that will be scoped into template.
	 * @param $model_alias (Optional) An alternative name for the model.
	 * @return string
	 */
	public static function get_admin_template($name, &$model, $model_alias = false) {
		$config = DispletReader_Woopa_Registry::get('config');
		$path = $config->admin_template_dir . DIRECTORY_SEPARATOR . $name;
		return self::_render($path, $model, $model_alias);
	}

	/**
	 * Echo a world-facing template
	 *
	 * $name is without path, path is found via global config object.
	 *
	 * @param $name Filename of template.
	 * @param &$model Model that will be scoped into template.
	 * @param $model_alias (Optional) An alternative name for the model.
	 */
	public static function draw_template($name, &$model, $model_alias = false) {
		echo self::get_template($name, $model, $model_alias);
	}

	/**
	 * Echo a backend-facing template
	 *
	 * $name is without path, path is found via global config object.
	 *
	 * @param $name Filename of template.
	 * @param &$model Model that will be scoped into template.
	 * @param $model_alias (Optional) An alternative name for the model.
	 */
	public static function draw_admin_template($name, &$model, $model_alias = false) {
		echo self::get_admin_template($name, $model, $model_alias);
	}

	/**
	 * @deprecated
	 */
	public function init($template, $model, $model_alias = false) {
		if ($model_alias) {
			$model_alias = (string) $model_alias;
			$$model_alias = &$model;
		}

		$this->_config = DispletReader_Woopa_Registry::get('config');

		$path = $this->_config->template_dir
			. DIRECTORY_SEPARATOR
			. $template;
	
		if (file_exists($path)) {
			ob_start();

			include $path;

			$output = ob_get_contents();

			ob_end_clean();
			return trim($output);
		}
	}
}
