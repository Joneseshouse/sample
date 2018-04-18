import assert from 'assert';
import forEach from 'lodash/forEach';
import isEqual from 'lodash/isEqual';
import keys from 'lodash/keys';
import Tools from 'utils/helpers/Tools';
import {FIELD_TYPE, URL_PREFIX} from 'app/constants';


describe('Tools', () => {
	it('getApiUrls', () => {
		const API_BASE_URL = Tools.getApiBaseUrl();

		const rawApiUrls = [
			{
				controller: 'config',
				endpoints: {
				    getItem: 'GET',
				    postItem: 'POST'
				}
			},{
				controller: 'articleDetail',
				endpoints: {
				    getArticle: 'GET',
				    postArticle: 'POST'
				}
			}
		];

		const eput = {
			getItem: {
				url: API_BASE_URL + 'config' + '/' + 'get-item',
				method: 'GET'
			},
			postItem: {
				url: API_BASE_URL + 'config' + '/' + 'post-item',
				method: 'POST'
			},
			articleDetailGetArticle: {
				url: API_BASE_URL + 'article-detail' + '/' + 'get-article',
				method: 'GET'
			},
			articleDetailPostArticle: {
				url: API_BASE_URL + 'article-detail' + '/' + 'post-article',
				method: 'POST'
			}
		}

		const output = Tools.getApiUrlsV1(rawApiUrls);
		forEach(eput, (eputItem, key) => {
			let outputItem = output[key];
			assert.equal(eputItem.url, outputItem.url);
			assert.equal(eputItem.method, outputItem.method);
		});
	});

	it('getHeadingData', () => {
		const data = {
	        title: {
	            title: 'Tên',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: null
	        }, type: {
	            title: 'Loại',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: 'article'
	        }, single: {
	            title: 'Dạng đơn',
	            type: FIELD_TYPE.BOOLEAN,
	            heading: false,
	            init: false
	        }
	    };

	    const expectedOutput = {
	        title: {
	            title: 'Tên',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: null
	        }, type: {
	            title: 'Loại',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: 'article'
	        }
	    };

		const output = Tools.getHeadingData(data);

		assert.equal(keys(output).length , keys(expectedOutput).length);
		forEach(output, (outputItem, key) => {
			let expectedOutputItem = expectedOutput[key];
			assert.equal(outputItem.title, expectedOutputItem.title);
			assert.equal(outputItem.type, expectedOutputItem.type);
			assert.equal(outputItem.heading, expectedOutputItem.heading);
			assert.equal(outputItem.init, expectedOutputItem.init);
		});
	});

	it('getInitData', () => {
		const data = {
	        title: {
	            title: 'Tên',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: 'some name'
	        }, type: {
	            title: 'Loại',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: 'article'
	        }, single: {
	            title: 'Dạng đơn',
	            type: FIELD_TYPE.BOOLEAN,
	            heading: false,
	            init: false
	        }
	    };

	    const expectedOutput = {
	    	id: null,
	        title: 'some name',
	        type: 'article',
	        single: false
	    };

		const output = Tools.getInitData(data);

		assert.equal(keys(output).length , keys(expectedOutput).length);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue, expectedOutput[key]);
		});
	});

	it('getRules', () => {
		const data = {
	        title: {
	            title: 'Tên',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: null,
	            rules: {
	                required: true,
	                min: 3
	            }
	        }, type: {
	            title: 'Loại',
	            type: FIELD_TYPE.STRING,
	            heading: true,
	            init: 'article',
	            rules: {
	                required: true
	            }
	        }, single: {
	            title: 'Dạng đơn',
	            type: FIELD_TYPE.BOOLEAN,
	            heading: true,
	            init: false
	        }
	    };

	    const expectedOutput = {
	    	title: {
				type: FIELD_TYPE.STRING,
				required: true,
				min: 3
			},type: {
				type: FIELD_TYPE.STRING,
				required: true
			}
	    };

		const output = Tools.getRules(data);

		assert.equal(keys(output).length , keys(expectedOutput).length);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.type, expectedOutput[key].type);
			if(typeof outputValue.required !== 'undefined'){
				assert.equal(outputValue.required, expectedOutput[key].required);
			}
			if(typeof outputValue.min !== 'undefined'){
				assert.equal(outputValue.min, expectedOutput[key].min);
			}
			if(typeof outputValue.max !== 'undefined'){
				assert.equal(outputValue.max, expectedOutput[key].max);
			}
		});
	});

	it('mapLabels', () => {
		const data = [
		    {id: 'article', title: 'Article'},
		    {id: 'banner', title: 'Banner'}
		];

	    const expectedOutput = {
	        article: 'Article',
	        banner: 'Banner'
	    };

		const output = Tools.mapLabels(data);

		assert.equal(keys(output).length , keys(expectedOutput).length);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue, expectedOutput[key]);
		});
	});

	it('toUrl', () => {
		const location = 'category';
		const params = ['article', '0'];

	    const expectedOutput = URL_PREFIX + 'category/article/0';

		const output = Tools.toUrl(location, params);

		assert.equal(output, expectedOutput);
	});

	it('urlParamsProcessing', () => {
		const rules = {
            location: '__type__',
            params: ['__some_id__', '--id--']
        };
		const fieldValue = {
			type: 'article',
			some_id: 4
		};
		const urlParams = {
			id: 5
		}
		const expectedOutput = Tools.toUrl('article', [4, 5]);
		const output = Tools.urlParamsProcessing(rules, fieldValue, urlParams);
		assert.equal(output, expectedOutput);
	});

	it('ignoreIndex', () => {
		const inputList = [
			{id: 1, title: 'Title 1'}, // 0
			{id: 2, title: 'Title 2'}, // 1
			{id: 3, title: 'Title 3'}, // 2
			{id: 4, title: 'Title 4'}, // 3
			{id: 5, title: 'Title 5'}, // 4
			{id: 6, title: 'Title 6'}, // 5
			{id: 7, title: 'Title 7'}  // 6
		];
		const listIgnoreIndex = [3, 5, 1];

	    const expectedOutput = [
			{id: 1, title: 'Title 1'}, // 0
			{id: 3, title: 'Title 3'}, // 2
			{id: 5, title: 'Title 5'}, // 4
			{id: 7, title: 'Title 7'}  // 6
		];
		const output = Tools.ignoreIndex(inputList, listIgnoreIndex);

		assert.equal(output.length, expectedOutput.length);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.id, expectedOutput[key].id);
			assert.equal(outputValue.title, expectedOutput[key].title);
		});
	});

	it('matchPrefix', () => {
		const prefix = '/admin/article/';
		let url = '/admin/article/9';

		let expectedOutput = true;
		let output = Tools.matchPrefix(prefix, url);
		assert.equal(output, expectedOutput);


		url = '/admin/banner/9'
		expectedOutput = false;
		output = Tools.matchPrefix(prefix, url);
		assert.equal(output, expectedOutput);
	});

	it('routeParse', () => {
		const routes = [
			{
				id: 1,
				module: "Article",
				title: "Bài viết",
				ascii_title: "bai viet",
				route: "api/v1/article/list",
				action: "Xem danh sách"
			}, {
				id: 2,
				module: "Article",
				title: "Bài viết",
				ascii_title: "bai viet",
				route: "api/v1/article/obj",
				action: "Xem chi tiết"
			}, {
				id: 3,
				module: "Banner",
				title: "Banner",
				ascii_title: "banner",
				route: "api/v1/banner/list",
				action: "Xem danh sách"
			}, {
				id: 4,
				module: "Banner",
				title: "Banner",
				ascii_title: "banner",
				route: "api/v1/banner/obj",
				action: "Xem chi tiết"
			}, {
				id: 5,
				module: "Article",
				title: "Bài viết",
				ascii_title: "bai viet",
				route: "api/v1/article/add",
				action: "Thêm mới"
			}
		];

		let expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];
		let output = Tools.routeParse(routes);

		assert.equal(output.length, expectedOutput.length);
		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});
	});

	it('routeDefault', () => {
		let inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: true
					}
				]
			}
		];

		let expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		let output = Tools.routeDefault(inputRoutes);

		assert.equal(output.length, expectedOutput.length);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});

	});

	it('routeApply', () => {
		let inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		let detail = ["api/v1/article/list", "api/v1/article/add", "api/v1/banner/obj"];

		let expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: true
					}
				]
			}
		];

		let output = Tools.routeApply(inputRoutes, detail);

		assert.equal(output.length, expectedOutput.length);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});

	});

	it('getAllowRoute', () => {
		let inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: true
					}
				]
			}
		];

		let expectedOutput = "api/v1/article/list,api/v1/article/add,api/v1/banner/obj";

		let output = Tools.getAllowRoute(inputRoutes);

		assert.equal(output, expectedOutput);
	});

	it('toggleRoute', () => {
		// case all empty
		let inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		let expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: true
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: true
					}
				]
			}
		];

		let output = Tools.toggleRoute(inputRoutes);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});

		// case all not empty
		inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: true
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: true
					}
				]
			}
		];

		output = Tools.toggleRoute(inputRoutes);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});

		// case all full
		inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: true
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: true
					}
				]
			}
		];

		expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		output = Tools.toggleRoute(inputRoutes);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});

		// case partial empty
		inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: true
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		output = Tools.toggleRoute(inputRoutes, 0);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});

		// case partial not empty
		inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: true
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: true
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		output = Tools.toggleRoute(inputRoutes, 0);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});

		// case partial full
		inputRoutes = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: true
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: true
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		expectedOutput = [
			{
				title: "Bài viết",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/article/list",
						allow: false
					}, {
						title: "Xem chi tiết",
						route: "api/v1/article/obj",
						allow: false
					}, {
						title: "Thêm mới",
						route: "api/v1/article/add",
						allow: false
					}
				]
			}, {
				title: "Banner",
				actions: [
					{
						title: "Xem danh sách",
						route: "api/v1/banner/list",
						allow: true
					}, {
						title: "Xem chi tiết",
						route: "api/v1/banner/obj",
						allow: false
					}
				]
			}
		];

		output = Tools.toggleRoute(inputRoutes, 0);

		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.actions.length, expectedOutput[key].actions.length);
			forEach(outputValue.actions, (action, subKey) => {
				action.title = expectedOutput[key].actions[subKey].title;
				action.route = expectedOutput[key].actions[subKey].route;
				action.allow = expectedOutput[key].actions[subKey].allow;
			});
		});
	});

	it('parseShopName', () => {
		let shopName = 'shop 1';
	    let expectedOutput = 'shop 1';
		let output = Tools.parseShopName(shopName);
		assert.equal(output, expectedOutput);

		shopName = 'http://abc.com/hello';
	    expectedOutput = 'http://abc.com';
		output = Tools.parseShopName(shopName);
		assert.equal(output, expectedOutput);

		shopName = 'http://abc.com/';
	    expectedOutput = 'http://abc.com';
		output = Tools.parseShopName(shopName);
		assert.equal(output, expectedOutput);

		shopName = 'http://abc.com';
	    expectedOutput = 'http://abc.com';
		output = Tools.parseShopName(shopName);
		assert.equal(output, expectedOutput);
	});

	/*
	it('orderItemsParse', () => {
		const data = [
		    {
		    	id: 1,
		    	title: 'Item 1',
		    	properties: 'p1',
		    	quantity: 3,
		    	unit_price: "23.5",
		    	url: 'http://abc.com/1',
		    	shop_name: 'shop 1',
		    	rate: 3400
		   	},{
		    	id: 2,
		    	title: 'Item 2',
		    	properties: 'p2',
		    	quantity: 2,
		    	unit_price: "20",
		    	url: 'http://abc.com/2',
		    	shop_name: 'shop 1',
		    	rate: 3400
		   	},{
		    	id: 3,
		    	title: 'Item 3',
		    	properties: 'p3',
		    	quantity: 4,
		    	unit_price: "10",
		    	url: 'http://abc.com/3',
		    	shop_name: 'shop 2',
		    	rate: 3400
		   	},{
		    	id: 4,
		    	title: 'Item 4',
		    	properties: 'p4',
		    	quantity: 6,
		    	unit_price: "12",
		    	url: 'http://p1.abc.com/3',
		    	shop_name: null,
		    	rate: 3400
		   	}
		];

	    const expectedOutput = {
	    	total: 222.5,
	    	selectedTotal: 0,
		    rate: 3400,
	    	shops: [
		    	{
		    		title: 'shop 1',
		    		total: 110.5,
		    		items: [
			    		{
					    	id: 1,
					    	stt: 1,
					    	checked: false,
					    	title: 'Item 1',
					    	properties: 'p1',
					    	quantity: 3,
					    	raw_unit_price: "23.5",
					    	url: 'http://abc.com/1',
					    	shop_name: 'shop 1',
			    			rate: 3400
					   	},{
					    	id: 2,
					    	stt: 2,
					    	checked: false,
					    	title: 'Item 2',
					    	properties: 'p2',
					    	quantity: 2,
					    	raw_unit_price: "20",
					    	url: 'http://abc.com/2',
					    	shop_name: 'shop 1',
			    			rate: 3400
					   	}
		    		]
		    	}, {
		    		title: 'shop 2',
		    		total: 40,
		    		items: [
			    		{
					    	id: 3,
					    	stt: 3,
					    	checked: false,
					    	title: 'Item 3',
					    	properties: 'p3',
					    	quantity: 4,
					    	raw_unit_price: "10",
					    	url: 'http://abc.com/3',
					    	shop_name: 'shop 2',
			    			rate: 3400
					   	}
		    		]
		    	}, {
		    		title: 'http://p1.abc.com',
		    		total: 72,
		    		items: [
			    		{
					    	id: 4,
					    	stt: 4,
					    	checked: false,
					    	title: 'Item 4',
					    	properties: 'p4',
					    	quantity: 6,
					    	raw_unit_price: "12",
					    	url: 'http://p1.abc.com/3',
					    	shop_name: 'http://p1.abc.com',
			    			rate: 3400
					   	}
		    		]
		    	}
		    ]
		};

		const output = Tools.orderItemsParse(data);

		assert.equal(output.length, expectedOutput.length);

		forEach(output, (outputValue, key) => {
			console.log(key);
			console.log(outputValue);
			assert.equal(outputValue.title, expectedOutput[key].title);
			assert.equal(outputValue.total, expectedOutput[key].total);
			forEach(outputValue.items, (item, key1) => {
				assert.equal(item.id, expectedOutput[key].items[key1].id);
				assert.equal(item.title, expectedOutput[key].items[key1].title);
				assert.equal(item.properties, expectedOutput[key].items[key1].properties);
				assert.equal(item.raw_unit_price, expectedOutput[key].items[key1].raw_unit_price);
				assert.equal(item.url, expectedOutput[key].items[key1].url);
				assert.equal(item.url, expectedOutput[key].items[key1].url);
				assert.equal(item.shop_name, expectedOutput[key].items[key1].shop_name);
				assert.equal(item.stt, expectedOutput[key].items[key1].stt);
				assert.equal(item.checked, expectedOutput[key].items[key1].checked);
				assert.equal(item.rate, expectedOutput[key].items[key1].rate);
			});
		});
	});
	*/
	it('isSameCollection', () => {
		const data1 = [
			{id: 1, title: 'title 1', order: 1},
			{id: 2, title: 'title 2', order: 2},
			{id: 3, title: 'title 3', order: 3},
			{id: 4, title: 'title 4', order: 4},
			{id: 5, title: 'title 5', order: 5}
	    ]

	    const data2 = [
			{title: 'title 1', order: 1, id: 1},
			{id: 2, title: 'title 2', order: 2},
			{id: 3, title: 'title 3', order: 3},
			{id: 4, title: 'title 4', order: 4},
			{id: 5, title: 'title 5', order: 5}
	    ]

		const output = Tools.isSameCollection(data1, data2);

		assert.equal(output , true);
	});

	it('renameColumn', () => {
		let listItem = [
			{id: 1, address: 'address 1', order: 1},
			{id: 2, address: 'address 2', order: 2},
			{id: 3, address: 'address 3', order: 3}
		];
	    let eput = [
			{id: 1, title: 'address 1', order: 1},
			{id: 2, title: 'address 2', order: 2},
			{id: 3, title: 'address 3', order: 3}
		];
		let output = Tools.renameColumn(listItem, 'address');
		forEach(output, (outputValue, key) => {
			assert.equal(outputValue.id, eput[key].id);
			assert.equal(outputValue.title, eput[key].title);
			assert.equal(outputValue.order, eput[key].order);
		});
	});

	it('dateFormat', () => {
		let input = '2017-04-25 16:40:59.000000';
	    let eput = '25/04/2017';
		let output = Tools.dateFormat(input);
		assert.equal(output, eput);
	});
});