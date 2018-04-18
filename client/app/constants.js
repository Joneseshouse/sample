export const APP_TITLE = '24HOrder';
export const APP_NAMESPACE = '24horder';
export const APP = window.location.href.split('://')[1].split('/')[1];
export const URL_PREFIX = APP?'/' + APP + '/':'/';
export const LOCAL_STORAGE_PREFIX = APP_NAMESPACE + '_' + APP;

export const API_PREFIX = '/api/v1/';
export const PROTOCOL = window.location.protocol + '//';
export const DOMAIN = window.location.host;
export const API_DOMAIN = window.location.host;
export const MEDIA_URL = PROTOCOL + API_DOMAIN + '/public/media/';
export const STATIC_URL = PROTOCOL + API_DOMAIN + '/public/static/';
export const BASE_URL = PROTOCOL + DOMAIN + URL_PREFIX;
export const API_URL = PROTOCOL + DOMAIN + API_PREFIX;

export const FIELD_TYPE = {
	STRING: 'STRING',
	EMAIL: 'EMAIL',
	INTEGER: 'INTEGER',
	BOOLEAN: 'BOOLEAN',
	INVERSE_BOOLEAN: 'INVERSE_BOOLEAN',
	FLOAT: 'FLOAT',
	DATE: 'DATE',
	DATETIME: 'DATETIME',
	IMAGE: 'IMAGE',
	FILE: 'FILE'
}

export const ADMIN_ROLES = [
	'quan-tri-vien'
];

export const USER_ROLES = [
	'khach-hang'
];

__webpack_public_path__ = APP?'/' + APP + '/':'/';
