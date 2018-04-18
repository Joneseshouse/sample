<?php

return [
	'max_size' => 4194304, # 4Mb
	'max_width' => 1200,
	'thumbnail_width' => 256,
	'golden_ratio' => 1.618,
	'allow_images' => [
		'image/gif',
		'image/jpeg',
		'image/png',
		'image/psd',
		'image/bmp',
		'image/tiff',
		'image/tiff',
		'image/jp2',
		'image/iff',
		'image/vnd.wap.wbmp',
		'image/xbm',
		'image/vnd.microsoft.icon',
	],
	'allow_pdf' => [
		'application/pdf',
	],
	'allow_text' => [
		'text/plain',
	],
	'allow_documents' => [
		'application/msword',
		'application/octet-stream',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.ms-word.document.macroenabled.12',
	],
	'allow_spreadsheets' => [
		'application/excel',
		'application/vnd.ms-excel',
		'application/x-excel',
		'application/x-msexcel',
		'application/vnd.ms-excel.sheet.binary.macroenabled.12',
		'application/vnd.ms-excel.sheet.macroenabled.12',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	],
	'allow_archives' => [
		'application/x-rar-compressed',
        'application/x-rar',
		'application/zip',
		'application/x-tar',
		'application/x-7z-compressed',
		'application/gzip',
		'application/tar',
		'application/tar+gzip',
		'application/x-gzip',
	],
];