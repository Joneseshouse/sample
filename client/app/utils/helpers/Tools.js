import React from 'react';
import Link from 'react-router/lib/Link';
import forEach from 'lodash/forEach';
import isEqual from 'lodash/isEqual';
import indexOf from 'lodash/indexOf';
import find from 'lodash/find';
import findIndex from 'lodash/findIndex';
import camelCase from 'lodash/camelCase';
import snakeCase from 'lodash/snakeCase';
import has from 'lodash/has';
import 'whatwg-fetch';
import Fingerprint2 from 'fingerprintjs2';
import {toastr} from 'react-redux-toastr';
import { push } from 'react-router-redux'

import store from 'app/store';
import {
    LOCAL_STORAGE_PREFIX,
    URL_PREFIX,
    API_PREFIX,
    PROTOCOL,
    DOMAIN,
    FIELD_TYPE
} from 'app/constants';

let fingerprint = null;

export default class Tools {
    static checkDevMode(){
        const domainArr = window.location.host.split('.');
        const suffix = domainArr[domainArr.length - 1];
        return ['dev'].indexOf(suffix) === -1 ? false : true;
    }

    static getApiBaseUrl(){
        return PROTOCOL + DOMAIN + API_PREFIX;
    }

    static getApiUrls(MAIN_CONTROLLER, rawApiUrls){
        let result = {};
        const API_BASE_URL = this.getApiBaseUrl();
        forEach(rawApiUrls, function(value, key){
            result[key] = {
                url: API_BASE_URL + MAIN_CONTROLLER + '/' +  key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase(),
                method: value
            }
        });
        return result;
    }

    static camelToSnake(input){
        return  input.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase()
    }

    static getApiUrlsV1(rawApiUrls){
        let result = {};
        const API_BASE_URL = this.getApiBaseUrl();
        forEach(rawApiUrls, (apiUrl, index) => {
            forEach(apiUrl.endpoints, (method, url) => {
                result[index === 0 ? camelCase(url) : camelCase(apiUrl.controller+'-'+url)] = {
                    url: API_BASE_URL + snakeCase(apiUrl.controller).replace(/_/g, '-') + '/' +  this.camelToSnake(url),
                    method
                }
            });
        });
        return result;
    }

    static get isAdmin(){
        if(!this.getStorage('authData')) return false;
        return this.getStorage('authData').role_type === 'admin';
    }

    static get isDatHang(){
        if(!this.getStorage('authData')) return false;
        return (['nhan-vien-dat-hang', 'quan-ly-dat-hang'].indexOf(this.getStorage('authData').role_type) !== -1);
    }

    static get isKeToan(){
        if(!this.getStorage('authData')) return false;
        return this.getStorage('authData').role_type === 'ke-toan';
    }

    static get isQuanTriVien(){
        if(!this.getStorage('authData')) return false;
        return this.getStorage('authData').role === 'quan-tri-vien';
    }

    static getVendor(url){
        if(url.match(/taobao.com/)){
            return "TAOBAO";
        }
        if(url.match(/tmall.com|tmall.hk|yao.95095.com/)){
            return "TMALL";
        }
        if(url.match(/1688.com|alibaba/)){
            return "1688";
        }
        return 'KHAC';
    }

    static sleep(ms=1){
        return new Promise(resolve => setTimeout(resolve, ms))
    }

    static toUrl(location='', params=[]){
        if(!params.length){
            return URL_PREFIX + location;
        }
        return URL_PREFIX + location + '/' + params.join('/');
    }

    static goToUrl(location='', params=[]){
        try{
            store.dispatch(push(this.toUrl(location, params)));
        }catch(error){
            console.error(error);
        }
    }

    static toggleGlobalLoading(show=true){
        const action = {
            type: 'TOGGLE_SPINNER',
            show
        }
        store.dispatch(action);
    }

    static popMessage(message, type='success'){
        message = this.errorMessageProcessing(message);
        if(message._error){
            message = message._error;
        }else{
            message = message[Object.keys(message)[0]];
            if(typeof message === 'object'){
                try{
                    message = message[0];
                }catch(error){
                    return;
                }
            }
        }
        if(type === 'success'){
            toastr.success('Success', message);
        }else{
            toastr.error('Error', message);
        }
    }

    static urlDataEncode(obj) {
        let str = [];
        for(let p in obj){
            if (has(obj, p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        }
        return str.join("&");
    }

    static urlDataDecode(str){
        // str = abc=def&ghi=aaa&ubuntu=debian
        let result = {};
        let arr = str.split('&');
        if(!str){
            return result;
        }
        forEach(arr, (value) => {
            let arrValue = value.split('=');
            if(arrValue.length===2){
                result[arrValue[0]] = arrValue[1];
            }
        });
        return result;
    }

    static getDecodedUrlData(url){
        let arr = url.split('?');
        if(arr.length === 1){
            return {};
        }
        return this.urlDataDecode(arr[1]);
    }

    static parseJson(input){
        try{
            return JSON.parse(input);
        }catch(error){
            return String(input);
        }
    }

    static checkLoginRequiredRoute(routes, target){
        if(!target){
            target = URL_PREFIX;
        }
        const index = findIndex(routes, {path: target});
        let login = false;
        if(index !== -1){
            if(routes[index].indexRoute){
                login = routes[index].indexRoute.params.login;
            }else{
                login = routes[index].params.login;
            }
        }
        return login;
    }

    static getStorage(key, defaultValue=null){
        let value = this.parseJson(localStorage.getItem(LOCAL_STORAGE_PREFIX + '_' + key));
        if(!value){
            value = defaultValue;
        }
        return value;
    }

    static getToken(){
        return this.getStorage('authData')?this.getStorage('authData').token:null;
    }

    static setStorage(key, value){
        try{
            let newValue = value;
            if(key === 'authData'){
                newValue = {...this.getStorage(key), ...value};
            }
            newValue = JSON.stringify(newValue);
            localStorage.setItem(LOCAL_STORAGE_PREFIX + '_' + key, newValue);
        }catch(error){
            console.error(error);
        }
    }

    static removeStorage(key){
        localStorage.removeItem(LOCAL_STORAGE_PREFIX + '_' + key);
    }

    static errorMessageProcessing(input){
        // console.error(input);
        if(typeof input === 'string'){
            return {_error: input};
        }
        let errors = {...input};
        let result = {};

        if(typeof errors === 'object'){
            forEach(errors, function(message, key){
                if(key === 'common'){
                    result._error = message;
                }else{
                    result[key] = message;
                }
            });
        }else{
            result = {_error: String(errors)};
        }
        return result;
    }

    static async getFingerPrint(){
        const result = await new Promise(function(resolve, reject){
            new Fingerprint2().get((newFingerprint) => {
                fingerprint = newFingerprint;
                resolve(newFingerprint);
            });
        });
        return result;
    }

    static getFingerprintFromLib(){
        return new Promise(function(resolve, reject){
            new Fingerprint2().get((newFingerprint) => {
                fingerprint = newFingerprint;
                resolve(newFingerprint);
            });
        });
    }

    static checkFingerprint(inner, ...args){
        return new Promise((resolve, reject) => {
            if(!fingerprint){
                this.getFingerprintFromLib().then((newFingerprint) => {
                    return inner(resolve, ...Array.prototype.slice.call(arguments).slice(1));
                });
            }else{
                return inner(resolve, ...Array.prototype.slice.call(arguments).slice(1));
            }
        });
    }

    //check value exist in array
    static isExistInArray(arr, value, index=0){
        if( typeof arr === 'object'){
            let arrList = [];
            forEach( arr, (item, key) => {
                if(item.id){
                    arrList.push(item.id);
                }
            });
            if(indexOf(arrList, value, index) >= 0){
                return true;
            }
            return false;
        }
    }

    static paramsProcessing(params){
        try{
            let requireFormData = false;
            forEach(params, (value, key) => {
                if(key !== 'id'){
                    if(value && typeof value === 'object'){
                        try{
                            value.item(0);
                            requireFormData = true;
                        }catch(error){
                            // Nothing change
                        }
                    }
                }
            });
            if(!requireFormData){
                return {
                    data: JSON.stringify(params),
                    contentType: "application/json"
                }
            }

            let formData = new FormData();
            forEach(params, (value, key) => {
                if(value && typeof value === 'object'){
                    if(value.length){
                        try{
                            formData.set(key, value.item(0));
                        }catch(error){
                            formData.set(key, JSON.stringify(value));
                        }
                    }
                }else{
                    formData.set(key, value);
                }
            });
            return {
                data: formData,
                contentType: null
            }
        }catch(error){
            console.error(error);
        }
    }

    static async apiCall(apiUrl, params={}, popMessage=true, usingLoading=true){
        try{
            if(usingLoading){
                this.toggleGlobalLoading();
            }
            // Fetch here
            let url = apiUrl.url;
            let requestConfig = {
                method: apiUrl.method,
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + this.getToken(),
                    "fingerprint": await this.getFingerPrint()
                },
                credentials: "same-origin"
            };
            if(apiUrl.method === 'POST'){
                // Have payload
                params = this.paramsProcessing(params);
                requestConfig.body = params.data;
                if(!params.contentType){
                    delete requestConfig.headers['Content-Type'];
                }
            }else{
                // No payload but url encode
                if(url.indexOf('?') === -1){
                    url += '?' + this.urlDataEncode(params);
                }
            }

            return fetch(url, requestConfig).then((response) => response.json()).then((result) => {
                if(result.status_code === 401){
                    this.removeStorage('authData');
                    this.goToUrl('login');
                }
                if(usingLoading){
                    this.toggleGlobalLoading(false);
                }

                if(result.status_code === 200){
                    if(popMessage){
                        this.popMessage(result.message, result.success?'success':'error');
                    }
                }else{
                    this.popMessage(result.message, result.success?'success':'error');
                }
                return result;
            }).catch((error) => {
                if(usingLoading){
                    this.toggleGlobalLoading(false);
                }
                this.popMessage(error, 'error');
                return error;
            });
        }catch(error){
            if(usingLoading){
                this.toggleGlobalLoading(false);
            }
            this.popMessage(error, 'error');
            console.error(error);
            return error;
        }
    }

    /*
    static apiCall(apiUrl, params={}, popMessage=true, usingLoading=true){
        try{
            if(usingLoading){
                this.toggleGlobalLoading();
            }
            return this.checkFingerprint((resolve, ...args) => {
                // Fetch here
                let url = apiUrl.url;
                let requestConfig = {
                    method: apiUrl.method,
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + this.getToken(),
                        "fingerprint": fingerprint
                    },
                    credentials: "same-origin"
                };
                if(apiUrl.method === 'POST'){
                    // Have payload
                    params = this.paramsProcessing(params);
                    requestConfig.body = params.data;
                    if(!params.contentType){
                        delete requestConfig.headers['Content-Type'];
                    }
                }else{
                    // No payload but url encode
                    if(url.indexOf('?') === -1){
                        url += '?' + this.urlDataEncode(params);
                    }
                }
                fetch(url, requestConfig).then((response) => {
                    if(!response.ok){
                        if(usingLoading){
                            this.toggleGlobalLoading(false);
                        }
                        // this.popMessage(error, 'error');
                        this.popMessage(response.statusText, 'error');
                        resolve(response.statusText);
                    }else{
                        response.text().then((responseText) => {
                            let result = {};
                            try{
                                result = JSON.parse(responseText);
                            }catch(error){
                                result.status_code = 400;
                                result.message = responseText;
                            }
                            if(result.status_code === 401){
                                this.removeStorage('authData');
                                this.goToUrl('login');
                            }
                            if(usingLoading){
                                this.toggleGlobalLoading(false);
                            }

                            if(result.status_code === 200){
                                if(popMessage){
                                    this.popMessage(result.message, result.success?'success':'error');
                                }
                            }else{
                                this.popMessage(result.message, result.success?'success':'error');
                            }

                            resolve(result);
                        });
                    }
                }, (error) => {
                    if(usingLoading){
                        this.toggleGlobalLoading(false);
                    }
                    this.popMessage(error, 'error');
                    resolve(error);
                });
            }, arguments);
        }catch(error){
            if(usingLoading){
                this.toggleGlobalLoading(false);
            }
            this.popMessage(error, 'error');
            console.error(error);
        }
    }
    */

    static getHeadingData(data){
        let result = {};
        forEach(data, (value, key) => {
            if(value.heading){
                let newItem = {};
                newItem[key] = value;
                result = {...result, ...newItem};
            }
        });
        return result;
    }

    static getInitData(data){
        let result = {id: null};
        forEach(data, (value, key) => {
            let newItem = {};
            newItem[key] = value.init;
            result = {...result, ...newItem};
        });
        return result;
    }

    static getRules(data){
        let result = {};
        forEach(data, (value, key) => {
            if(typeof value.rules === 'object'){
                result[key] = {
                    type: value.type,
                    ...value.rules
                };
            }
        });
        return result;
    }

    static mapLabels(data, inputKey='id', inputValue='title'){
        if(!data){
            return {};
        }
        let result = {};
        forEach(data, value => {
            result[value[inputKey]] = value[inputValue];
        });
        return result;
    }

    static urlParamsProcessing(inputRules, fieldValue, urlParams={}){
        /*
        const rules = {
            location: '__type__',
            params: ['__some_id__', '--id--']
        };
        */

        const rules = {...inputRules};
        let result = [];

        let location = rules.location;
        if(rules.location.indexOf('__') !== -1){ // FieldValue
            location = fieldValue[rules.location.replace(/__/g, '')];
        }else if(rules.location.indexOf('--') !== -1){ // UrlParams
            location = urlParams[rules.location.replace(/--/g, '')];
        }

        forEach(rules.params, (value) => {
            if(value.indexOf('__') !== -1){
                value = fieldValue[value.replace(/__/g, '')];
            }else if(value.indexOf('--') !== -1){
                value = urlParams[value.replace(/--/g, '')];
            }
            result.push(value);
        });

        return Tools.toUrl(location, result);
    }

    static ignoreIndex(inputList, listIgnoreIndex){
        let result = [];
        forEach(inputList, (value, key) => {
            if(listIgnoreIndex.indexOf(key) === -1){
                result.push(value);
            }
        });
        return result;
    }


    static formatTableData(displayKeys, row, key, reducer){
        let value = null;
        switch(displayKeys[key].type){
            case FIELD_TYPE.STRING:
                if(typeof displayKeys[key].mapLabels !== 'undefined'){
                    const mapLabels = this.mapLabels(reducer[displayKeys[key].mapLabels]);
                    value = mapLabels[row[key]];
                }else{
                    if(row[key] && typeof row[key] === 'object'){
                        value = row[key].date;
                        if(value.split('.').length === 2){
                            value = value.split('.')[0];
                        }
                    }else{
                        value = row[key];
                    }
                }
                if(typeof displayKeys[key].date !== 'undefined' && displayKeys[key].date){
                    value = Tools.dateFormat(value);
                }
                break;
            case FIELD_TYPE.EMAIL:
                value = row[key];
                break;
            case FIELD_TYPE.INTEGER:
                if(typeof displayKeys[key].mapLabels !== 'undefined'){
                    const mapLabels = this.mapLabels(reducer[displayKeys[key].mapLabels]);
                    value = mapLabels[row[key]];
                }else{
                    value = this.numberFormat(row[key]);
                }
                if(typeof displayKeys[key].suffix !== 'undefined'){
                    value = value + ' ' + displayKeys[key].suffix;
                }
                if(typeof displayKeys[key].prefix !== 'undefined'){
                    value = displayKeys[key].prefix + ' ' + value;
                }
                break;
            case FIELD_TYPE.FLOAT:
                value = this.numberFormat(Math.round(row[key]*10)/10);
                if(typeof displayKeys[key].suffix !== 'undefined'){
                    value = value + ' ' + displayKeys[key].suffix;
                }
                if(typeof displayKeys[key].prefix !== 'undefined'){
                    value = displayKeys[key].prefix + ' ' + value;
                }
                break;
            case FIELD_TYPE.BOOLEAN:
                if(row[key] === null) return null;
                if(row[key]){
                    value = <span className="glyphicon glyphicon-ok green"></span>;
                }else{
                    value = <span className="glyphicon glyphicon-remove red"></span>;
                }
                break;
            case FIELD_TYPE.INVERSE_BOOLEAN:
                if(row[key]){
                    value = <span className="glyphicon glyphicon-remove red"></span>;
                }else{
                    value = <span className="glyphicon glyphicon-ok green"></span>;
                }
                break;
            case FIELD_TYPE.DATE:
                value = this.dateFormat(row[key]);
                break;
            default:
                // value = null
                value = row[key];
        }
        return value;
    }

    static boolToSymbol(input){
        if(input){
            return <span className="glyphicon glyphicon-ok green"></span>;
        }
        return <span className="glyphicon glyphicon-remove red"></span>;
    }

    static tableData(displayKeys, row, key, urlParams={}, reducer={}){
        let value = this.formatTableData(displayKeys, row, key, reducer);
        if(displayKeys[key].link){
            value = (
                <Link
                    to={Tools.urlParamsProcessing(displayKeys[key].link, row, urlParams)}>
                    {value}
                </Link>
            );
        }
        return value;
    }

    static matchPrefix(prefix, url){
        if(!prefix || !url){
            return false;
        }
        if(url.indexOf(prefix) === 0){
            return true;
        }
        return false;
    }

    static routeParse(routes){
        let result = [];
        forEach(routes, route => {
            let index = findIndex(result, {'title': route.title});
            let action = {
                title: route.action,
                route: route.route,
                allow: false
            };
            if(index === -1){
                // Module not inserted yet.
                result.push({
                    title: route.title,
                    actions: [action]
                });
            }else{
                // Module existed.
                result[index].actions.push(action);
            }
        });
        return result;
    }

    static routeDefault(inputRoutes){
        let routes = [...inputRoutes];
        forEach(routes, (module, i) => {
            forEach(module.actions, (action, j) => {
                routes[i].actions[j].allow = false;
            });
        });
        return routes;
    }

    static routeApply(inputRoutes, detail){
        let routes = this.routeDefault(inputRoutes);

        forEach(routes, (module, i) => {
            forEach(module.actions, (action, j) => {
                if(detail.indexOf(action.route) !== -1){
                    routes[i].actions[j].allow = true;
                }
            });
        });
        return routes;
    }

    static getAllowRoute(routes){
        let result = [];
        forEach(routes, (module, i) => {
            forEach(module.actions, (action, j) => {
                if(routes[i].actions[j].allow){
                    result.push(routes[i].actions[j].route);
                }
            });
        });
        return result.join(',');
    }

    static toggleRoute(inputRoutes, index=null){
        let routes = [...inputRoutes];
        let isFull = false;
        let counter = 0;
        let totalItem = 0;
        if(index !== null){
            forEach(routes[index].actions, (action, j) => {
                totalItem++;
                if(routes[index].actions[j].allow){
                    counter++;
                }
            });

            if(counter === totalItem){
                isFull = true;
            }

            if(isFull){
                forEach(routes[index].actions, (action, j) => {
                    routes[index].actions[j].allow = false;
                });
            }else{
                forEach(routes[index].actions, (action, j) => {
                    routes[index].actions[j].allow = true;
                });
            }
        }else{
            forEach(routes, (module, i) => {
                forEach(module.actions, (action, j) => {
                    totalItem++;
                    if(routes[i].actions[j].allow){
                        counter++;
                    }
                });
            });

            if(counter === totalItem){
                isFull = true;
            }

            if(isFull){
                forEach(routes, (module, i) => {
                    forEach(module.actions, (action, j) => {
                        routes[i].actions[j].allow = false;
                    });
                });
            }else{
                forEach(routes, (module, i) => {
                    forEach(module.actions, (action, j) => {
                        routes[i].actions[j].allow = true;
                    });
                });
            }
        }

        return routes;
    }

    static parseShopName(shopName=null){
        if(!shopName){
            return shopName;
        }
        if(shopName.includes('://')){
            let shopNameArr = shopName.split('://');
            let protocol = shopNameArr[0];
            let domain = shopNameArr[1].split('/')[0];
            return protocol + '://' + domain;
        }
        return shopName;
    }

    static orderItemsParse(data){
        let result = {
            total: 0,
            selectedTotal: 0,
            rate: 0,
            quantity: 0,
            links: data.length,
            shops: []
        };
        forEach(data, (item, key) => {
            let shop_name = item.shop_name?item.shop_name:this.parseShopName(item.url);
            let shopIndex = findIndex(result.shops, {title: shop_name});
            let total = parseInt(item.quantity) * parseFloat(item.unit_price);
            let rate = item.rate;
            let checked = false;
            if(!result.rate){
                result.rate = item.rate;
            }
            result.total += total;
            result.quantity += parseInt(item.quantity);
            if(shopIndex === -1){
                // New shop
                result.shops.push({
                    title: shop_name,
                    total,
                    items: [
                        {...item, shop_name, checked, stt: key + 1}
                    ]
                });
            }else{
                // Old shop
                result.shops[shopIndex].total += total;
                result.shops[shopIndex].items.push({...item, shop_name, checked, stt: key + 1});
            }
        });
        return result;
    }

    static numberFormat(number){
        var formater = new Intl.NumberFormat();
        return formater.format(number); // "1,234,567,890"
    }
    /*
    static dateFormat(date, locale="FR-FR"){
        // {date: "2017-04-25 16:40:59.000000", timezone_type: 3, timezone: "Asia/Saigon"}
        try{
            if(!date) return null;
            let dateString = null;
            if(typeof date === 'string'){
                dateString = date.replace(' ', 'T');
            }
            if(typeof date === 'object'){
                dateString = date.date.replace(' ', 'T');
            }
            try{
                date = new Date(dateString);
                const formater = new Intl.DateTimeFormat(locale);
                return formater.format(date);
            }catch(error){
                return new Date();
            }
        }catch(error){
            console.error(error);
            return date;
        }
    }
    */

    static dateFormat(date){
        // {date: "2017-04-25 16:40:59.000000", timezone_type: 3, timezone: "Asia/Saigon"}
        try{
            if(!date) return null;
            let dateString = null;
            if(typeof date === 'string'){
                dateString = date.split(' ')[0].split('-').reverse().join('/');
            }
            if(typeof date === 'object'){
                dateString = date.date.split(' ')[0].split('-').reverse().join('/');
            }
            return dateString;
        }catch(error){
            console.error(error);
            return date;
        }
    }

    static isSameCollection(oldList, newList){
        let diffLength = false;
        let diffValue = false;
        if(oldList.length !== newList.length){
            diffLength = true;
        }
        if(!diffLength){
            forEach(oldList, (value, key) => {
                if(!isEqual(value, newList[key])){
                    diffValue = true;
                }
            });
        }
        if(diffLength || diffValue){
            return false;
        }
        return true;
    }

    static renameColumn(listItem, fromColumn, toColumn='title'){
        let result = [];
        forEach(listItem, item => {
            let newItem = {...item};
            newItem[toColumn] = newItem[fromColumn];
            delete newItem[fromColumn];
            result.push({...newItem});
        });
        return result;
    }
}
