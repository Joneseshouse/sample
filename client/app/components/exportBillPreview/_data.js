import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'exportBill',
        endpoints: {
            obj: 'GET',
            editContact: 'POST'
        }
    }
];


export const labels = {
	common: {
		title: 'Quản lý xuất hàng'
	},
    mainForm: {
        contact_id:{
            title: 'Địa chỉ',
            type: FIELD_TYPE.STRING,
            init: 0,
            rules: {
            }
        },
        cn_store_date: {
            title: 'Ngày về kho TQ',
            type: FIELD_TYPE.STRING,
            date: true,
            width: 60,
            heading: true,
            init: null,
            rules: {
            }
        },
        code: {
            title: 'Mã vận đơn',
            type: FIELD_TYPE.STRING,
            width: 120,
            heading: true,
            init: null,
            rules: {
            }
        }, address_code: {
            title: 'Mã địa chỉ',
            type: FIELD_TYPE.STRING,
            width: 60,
            heading: true,
            init: null,
            rules: {
            }
        }, mass: {
            title: 'Khối lượng',
            type: FIELD_TYPE.FLOAT,
            width: 60,
            suffix: 'Kg',
            heading: true,
            init: null,
            rules: {
            }
        },
        packages: {
            title: 'Số kiện',
            type: FIELD_TYPE.INTEGER,
            width: 60,
            heading: true,
            init: null,
            rules: {
            }
        },
        sub_fee: {
            title: 'Phí giao hàng VND',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
                required: true
            }
        },
        note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
