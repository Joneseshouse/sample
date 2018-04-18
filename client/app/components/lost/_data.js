import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'lost',
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
		title: 'Quản lý hàng thất lạc'
	},
    mainForm: {
        updated_at: {
            title: 'Ngày tháng',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        bill_of_landing_id:{
            title: "Mã vận đơn",
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            mapLabels: 'listBillLost',
            rules:{
                required: true
            }
        },description:{
            title: "Mô tả",
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules:{
            }
        }, preview:{
            title: "Mô tả vắn tắt",
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules:{
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
