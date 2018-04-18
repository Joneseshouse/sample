import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'rateLog',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    }
];


export const labels = {
	common: {
		title: 'Quản lý rateLog'
	},
    mainForm: {
        created_at: {
            title: 'Ngày',
            type: FIELD_TYPE.STRING,
            date: true,
            heading: true,
            init: null,
            rules: {
            }
        },
        rate: {
            title: 'Tỷ giá mua vào',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        buy_rate: {
            title: 'Tỷ giá chuyển khoản',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        sell_rate: {
            title: 'Tỷ giá nhờ thanh toán',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        order_rate: {
            title: 'Tỷ giá order',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
