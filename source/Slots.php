<?php

class Slots {

	private $view;
	private $buffers;
	private $files;
	private $openBuffers;

	private static $parentKey = '${_PARENT_REPLACE_KEY_}';
	/**
	 */
	public function __construct(\View $view) {
		$this->view = $view;
		$this->files =
		$this->openBuffers =
		$this->buffers = [];
	}

	/**
	 */
	public function has($key) {
		return isset($this->buffers[$key]);
	}

	/**
	 */
	public function get($key) {
		return $this->buffers[$key];
	}

	public function super() {
		if ($this->openBuffers) {
			echo self::$parentKey;
		}
	}

	/**
	 */
	public function output($key, $default="") {
		$this->set($key, $default);

		echo $this->buffers[$key];
	}

	/**
	 */
	public function start($key) {
		$this->openBuffers[] = $key;
		ob_start();
	}

	/**
	 */
	public function stop() {
		$key = array_pop($this->openBuffers);

		$newBuffer = rtrim(ob_get_clean());

		if ($this->has($key)) {
			$buffer = $this->get($key);
			if (strpos($buffer, self::$parentKey) !== false) {
				$newBuffer = str_replace(self::$parentKey, $newBuffer, $buffer);
			} else {
				$newBuffer = null;
			}
		}

		if ($newBuffer) {
			$this->buffers[$key] = $newBuffer;
		}

		return $key;
	}

	public function set($key, $output) {
		$this->start($key);
		echo $output;
		$this->stop();
	}

	/**
	 */
	public function outputStop() {
		$key = $this->stop();
		$this->output($key);
	}

}
