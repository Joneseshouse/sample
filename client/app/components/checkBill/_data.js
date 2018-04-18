import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'checkBill',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            checkFull: 'POST',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    }, {
        controller: 'billOfLanding',
        endpoints: {
            listCheckBill: 'GET',
            checkDuplicateCode: 'GET'
        }
    }, {
        controller: 'orderItemNote',
        endpoints: {
            list: 'GET',
            obj: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    }
];


export const labels = {
    common: {
        title: 'Quản lý kiểm hàng'
    },
    mainForm: {
        created_at: {
            title: 'Ngày kiểm',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        bill_of_landing_code: {
            title: 'Vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        address_uid: {
            title: 'Mã địa chỉ',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        admin_full_name: {
            title: 'Nhân viên kiểm',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    },
    depositForm: {
        packages: {
            title: 'Số kiện',
            type: FIELD_TYPE.INTEGER,
            init: 1,
            rules: {
                required: true
            }
        },
        input_mass: {
            title: 'Khối lượng',
            type: FIELD_TYPE.FLOAT,
            init: 0,
            rules: {
            }
        },
        length: {
            title: 'Dài',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        width: {
            title: 'Rộng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        height: {
            title: 'Cao',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        sub_fee: {
            title: 'Phụ phí',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
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
