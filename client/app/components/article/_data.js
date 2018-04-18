import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'article',
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
		title: 'Quản lý bài viết'
	},
    mainForm: {
        title: {
            title: 'Tiêu đề bài viết',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            link: {
                location: 'article/detail',
                params: ['__id__']
            },
            rules: {
                required: true,
                min: 3
            }
        },
        order: {
            title: 'Thứ tự',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
            }
        }
    },
    detailForm: {
        title: {
            title: 'Tiêu đề bài viết',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            link: {
                location: 'article/detail',
                params: ['__id__']
            },
            rules: {
                required: true,
                min: 3
            }
        },
        slug: {
            title: 'Slug',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        },
        order: {
            title: 'Thứ tự',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
                required: true
            }
        },
        thumbnail: {
            title: 'Ảnh đại diện',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        },
        content: {
            title: 'Nội dung',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
