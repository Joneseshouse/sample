import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'purchase',
        endpoints: {
            upload: 'POST'
        }
    }
];


export const labels = {
	common: {
		title: 'Quản lý nhập liệu'
	},
    mainForm: {
        purchase_code: {
            title: 'Mã giao dịch',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {}
        }, bill_of_landing_code: {
            title: 'Mã vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {}
        }, real_amount: {
            title: 'Thanh toán thực',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            prefix: '¥',
            init: null,
            rules: {}
        }, note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {}
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
