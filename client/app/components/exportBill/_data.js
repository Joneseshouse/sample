import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'exportBill',
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
		title: 'Quản lý xuất hàng'
	},
    mainForm: {
        created_at: {
            title: 'Ngày xuất',
            type: FIELD_TYPE.STRING,
            date: true,
            heading: true,
            init: null,
            rules: {
            }
        },
        uid: {
            title: 'Mã đơn xuất hàng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        admin_fullname: {
            title: 'Nhân viên',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
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
        }
        , sub_fee: {
            title: 'Phụ phí',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }, amount: {
            title: 'Tổng đơn',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: null,
            rules: {
            }
        }, total: {
            title: 'Tổng cộng',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: null,
            rules: {
            }
        }, note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    },
    filterForm: {
        date_range: {
            title: '',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        admin_id: {
            title: '',
            placeholder: 'Nhân viên xuất',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        uid: {
            title: '',
            placeholder: 'Mã đơn xuất',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        address_code: {
            title: '',
            placeholder: 'Mã địa chỉ',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
