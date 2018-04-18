import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'receipt',
        endpoints: {
            obj: 'GET'
        }
    }
];


export const labels = {
	common: {
		title: 'In phiếu thu'
	},
    mainForm: {}
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
