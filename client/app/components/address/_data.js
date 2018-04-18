import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'address',
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
		title: 'Quản lý địa chỉ nhận hàng'
	},
    mainForm: {
        uid: {
            title: 'Mã',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        area_code_id: {
            title: 'Vùng',
            type: FIELD_TYPE.INTEGER,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        },
        area_code_uid: {
            title: 'Vùng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        delivery_fee_unit: {
            title: 'Đơn giá v.chuyển',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            prefix: '₫',
            rules: {
            }
        },
        address: {
            title: 'Địa chỉ',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        },
        phone: {
            title: 'Số điện thoại',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        fullname: {
            title: 'Họ tên',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        default: {
            title: 'Mặc định',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: false,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
