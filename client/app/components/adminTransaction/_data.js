import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'adminTransaction',
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
		title: 'Quản lý kế toán nội bộ'
	},
    mainForm: {
        updated_at: {
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
            title: 'Admin',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        target_admin_fullname: {
            title: 'Nhân viên',
            type: FIELD_TYPE.STRING,
            heading: Tools.isAdmin,
            init: null,
            rules: {
            }
        },
        admin_id: {
            title: 'Admin',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
                required: true
            }
        },
        target_admin_id: {
            title: 'Nhân viên',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
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
            mapLabels: 'listMoenyType',
            rules: {
                required: true
            }
        },
        amount: {
            title: 'Giá trị giao dịch',
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
        /*
        balance: {
            title: 'Số dư',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        */
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
            title: 'Admin',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        target_admin_id: {
            title: 'Nhân viên',
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
