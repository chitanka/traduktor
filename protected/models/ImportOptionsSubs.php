<?php
class ImportOptionsSubs extends CFormModel {
	public $src, $format, $encoding;

	public function rules() {
		return array(
			// login and pass are required
			array("src", "file", "message" => "Пожалуйста, выберите файл.", "maxSize" => 1024 * 1024, "minSize" => 1,
				"tooLarge" => "Файлът е прекалено голям", "tooSmall" => "Файлът е подозрително малък",
			),
			array("format", "required"),
			array("encoding", "required"),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			"src" => "Файл със субтитри",
			"format" => "Формат",
			"encoding" => "Кодировка",
		);
	}

}
