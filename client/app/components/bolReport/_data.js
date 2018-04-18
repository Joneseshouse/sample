import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'bolReport',
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
		title: 'Quản lý bolReport'
	},
    filterForm:{
        selected_year: {
            title: 'Năm',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
                required: true
            }
        },selected_month: {
            title: 'Tháng',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
                required: true
            }
        }
    },
    mainForm: {
        report_date: {
            title: 'Ngày',
            type: FIELD_TYPE.STRING,
            date: true,
            heading: true,
            init: null,
            rules: {
            }
        }, number_of_bills: {
            title: 'S.L bill',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
            }
        }, number_of_packages: {
            title: 'Số kiện',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
            }
        }, total_mass: {
            title: 'K.Lượng',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            rules: {
            }
        }, cn_normal: {
            title: 'Order',
            type: FIELD_TYPE.INTEGER,
            group: 'China nhận',
            heading: true,
            init: null,
            rules: {
            }
        }, cn_deposit: {
            title: 'V.Chuyển',
            type: FIELD_TYPE.INTEGER,
            group: 'China nhận',
            heading: true,
            init: null,
            rules: {
            }
        }, cn_missing_info: {
            title: 'Thiếu T.Tin',
            type: FIELD_TYPE.INTEGER,
            group: 'China nhận',
            heading: true,
            init: null,
            rules: {
            }
        }, vn_normal: {
            title: 'Order',
            type: FIELD_TYPE.INTEGER,
            group: 'Kho VN nhận',
            heading: true,
            init: null,
            rules: {
            }
        }, vn_deposit: {
            title: 'V.Chuyển',
            type: FIELD_TYPE.INTEGER,
            group: 'Kho VN nhận',
            heading: true,
            init: null,
            rules: {
            }
        }, vn_missing_info: {
            title: 'Thiếu T.Tin',
            type: FIELD_TYPE.INTEGER,
            group: 'Kho VN nhận',
            heading: true,
            init: null,
            rules: {
            }
        }, export_normal: {
            title: 'Order',
            type: FIELD_TYPE.INTEGER,
            group: 'Xuất',
            heading: true,
            init: null,
            rules: {
            }
        }, export_deposit: {
            title: 'V.Chuyển',
            type: FIELD_TYPE.INTEGER,
            group: 'Xuất',
            heading: true,
            init: null,
            rules: {
            }
        },
        /*
        export_missing_info: {
            title: 'Thiếu T.Tin',
            type: FIELD_TYPE.INTEGER,
            group: 'Xuất',
            heading: true,
            init: null,
            rules: {
            }
        },
        */
        inventory: {
            title: 'Tồn kho',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
