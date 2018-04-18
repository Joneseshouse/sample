import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'cnBillOfLanding',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST',
            upload: 'POST'
        }
    }
];

export const labels = {
	common: {
		title: 'Quản lý vận đơn TQ'
	},
    mainForm: {
        created_at: {
            title: 'Ngày',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        code: {
            title: 'Mã vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        address_uid: {
            title: 'Mã địa chỉ',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        input_mass: {
            title: 'Khối lượng',
            type: FIELD_TYPE.FLOAT,
            init: null,
            rules: {
                required: true
            }
        },
        mass: {
            title: 'Khối lượng',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            rules: {
            }
        },
        length: {
            title: 'Dài',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
            }
        },
        width: {
            title: 'Rộng',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
            }
        },
        height: {
            title: 'Cao',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
            }
        },
        packages: {
            title: 'Số kiện',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 1,
            rules: {
                required: true
            }
        },
        sub_fee: {
            title: 'Phụ phí VNĐ',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
                required: true
            }
        },
        note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    },
    uploadForm: {
        list_code: {
            title: 'Danh sách vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
