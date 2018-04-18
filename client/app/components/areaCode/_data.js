import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'areaCode',
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
		title: 'Quản lý mã vùng'
	},
    mainForm: {
        title: {
            title: 'Tên vùng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                max: 100
            }
        }, code: {
            title: 'Mã vùng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                max: 4
            }
        }, delivery_fee_unit: {
            title: 'Đơn giá vận chuyển',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
                required: true,
                min: 0
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
