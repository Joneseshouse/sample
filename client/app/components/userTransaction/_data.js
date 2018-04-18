import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'userTransaction',
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
		title: 'Quản lý kế toán khách hàng'
	},
    mainForm: {
        created_at: {
            title: 'Ngày tạo / sửa',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        uid: {
            title: 'Mã',
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
        user_fullname: {
            title: 'Khách hàng',
            type: FIELD_TYPE.STRING,
            heading: Tools.isAdmin,
            init: null,
            rules: {
            }
        },
        admin_id: {
            title: 'Nhân viên',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
                required: true
            }
        },
        user_id: {
            title: 'Khách hàng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
                required: true
            }
        },
        type: {
            title: 'Loại giao dịch',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: '',
            mapLabels: 'listType',
            rules: {
                required: true
            }
        },
        money_type: {
            title: 'Loại tiền',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: '',
            mapLabels: 'listMoneyType',
            rules: {
                required: true
            }
        },
        amount: {
            title: 'Số tiền',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: 0,
            rules: {
                required: true
            }
        },
        credit_balance: {
            title: 'Ghi có',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        liabilities: {
            title: 'Ghi nợ',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        balance: {
            title: 'Số dư',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        purchasing: {
            title: 'Đang giao dịch',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        missing: {
            title: 'Còn thiếu',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }
    },
    filterForm: {
        uid: {
            title: 'Mã',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        admin_id: {
            title: 'Nhân viên',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        user_id: {
            title: 'Khách hàng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        type: {
            title: 'Hành động',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: '',
            mapLabels: 'listType',
            rules: {
            }
        },
        note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        from_amount: {
            title: 'Giá trị từ',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        to_amount: {
            title: 'đến',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        date_range: {
            title: 'Khoảng ngày',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        money_type: {
            title: 'Loại tiền',
            type: FIELD_TYPE.STRING,
            init: '',
            mapLabels: 'listMoneyType',
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
