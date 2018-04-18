import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'exportBillDaily',
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
		title: 'Quản lý exportBillDaily'
	},
    mainForm: {
        export_date: {
            title: 'Ngày xuất',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        number_of_export: {
            title: 'Số đơn xuất',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
            }
        },
        addresses: {
            title: 'Mã địa chỉ',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        mass: {
            title: 'Khối lượng',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: 0,
            rules: {
            }
        },
        packages: {
            title: 'Số kiện',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
            }
        },
        number_of_bol: {
            title: 'Số vận đơn',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
            }
        },
        sub_fee: {
            title: 'Phụ phí',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
            }
        },
        amount: {
            title: 'Tổng đơn',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
            }
        },
        total: {
            title: 'Tổng cộng',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
