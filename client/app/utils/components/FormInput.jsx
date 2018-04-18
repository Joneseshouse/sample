import React from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import Dropzone from 'react-dropzone';
import CustomDatePicker from './CustomDatePicker';
import CustomDateRangePicker from './CustomDateRangePicker';
import {APP, MEDIA_URL} from 'app/constants';

import ReactSummernoteLoader from 'utils/components/ReactSummerNoteLoader';
import Tools from 'helpers/Tools';
import {apiUrls} from 'components/attach/_data';


class FormInput extends React.Component {
    static propTypes = {
        clearable: PropTypes.bool,
        showLabel: PropTypes.bool,
        show: PropTypes.bool,
        focus: PropTypes.bool,
        loader: PropTypes.func
    };
    static defaultProps = {
        clearable: false,
        showLabel: true,
        show: true,
        focus: false
    };

    constructor(props) {
        super(props);
        this.state = {
            previewImage: null,
            fileName: null
        };
        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleSelectChange = this.handleSelectChange.bind(this);
        this.imageUploadHanlde = this.imageUploadHanlde.bind(this);
        this._renderImage = this._renderImage.bind(this);
        this._renderLabel = this._renderLabel.bind(this);
    }

    componentDidMount(){
        if(this.props.type === 'richtext'){
            ReactSummernoteLoader().then(ReactSummernote => {
                this.setState({ ...ReactSummernote });
            });
        }
        if(this.props.input.value && this.props.type === 'image'){
            this.setState({previewImage: MEDIA_URL + this.props.input.value});
        }
    }

    handleInputChange(event){
        if(typeof this.props.onInputChange !== 'undefined'){
            this.props.onInputChange(event.target.value);
        }
        this.props.input.onChange(event);
    }

    handleSelectChange(value){
        this.props.onInputChange(value.id);
        this.props.input.onChange(value.id);
    }

    _renderError(touched, dirty, error){
        if((touched || dirty) && error){
            return (
                <h6 className="text-danger">{error}</h6>
            );
        }
        return null;
    }

    _renderInput(){
        try{
            const touched = this.props.meta.touched;
            const dirty = this.props.meta.dirty;
            const error = this.props.meta.error;
            if(this.props.type === 'float'){
                return (
                    <div className="form-group">
                        {this._renderLabel()}
                        <input
                            {...this.props.input}
                            className="form-control"
                            step="0.01"
                            onChange={event => this.handleInputChange(event)}
                            autoFocus={this.props.focus}
                            placeholder={this.props.label.placeholder}
                            type={this.props.type}/>
                        {this._renderError(touched, dirty, error)}
                    </div>
                );
            }else{
                return (
                    <div className="form-group">
                        {this._renderLabel()}
                        <input
                            {...this.props.input}
                            className="form-control"
                            onChange={event => this.handleInputChange(event)}
                            autoFocus={this.props.focus}
                            placeholder={this.props.label.placeholder}
                            type={this.props.type}/>
                        {this._renderError(touched, dirty, error)}
                    </div>
                );
            }
        }catch(error){
            console.error(error);
            return null;
        }
    }

    _renderImagePreview(previewImage=null){
        if(previewImage){
            return (
                <div>
                    <img src={previewImage} className="preview-image"/>
                </div>
            );
        }
        return null;
    }

    _renderFileName(fileName=null){
        if(fileName){
            return (
                <div className="drop-zone-file-name">{fileName}</div>
            );
        }
        return (
            <div>Chọn file hoặc kéo thả...</div>
        );
    }

    _renderImage(){
        try{
            const touched = this.props.meta.touched;
            const dirty = this.props.meta.dirty;
            const error = this.props.meta.error;
            return (
                <div className="form-group">
                    {this._renderLabel()}
                    {this._renderImagePreview(this.state.previewImage)}
                    <Dropzone
                        multiple={true}
                        className="drop-zone"
                        {...this.props.input}
                        onDrop={(file) => {
                            this.props.input.onChange(file);
                            var reader = new FileReader();
                            reader.readAsDataURL(file[0]);
                            reader.onload = () => {
                                this.setState({
                                    previewImage: reader.result,
                                    fileName: file[0].name
                                });
                            };
                        }}>
                        {this._renderFileName(this.state.fileName)}
                    </Dropzone>

                    {this._renderError(touched, dirty, error)}
                </div>
            );
        }catch(error){
            console.error(error);
            return null;
        }
    }

    _renderFile(){
        try{
            const touched = this.props.meta.touched;
            const dirty = this.props.meta.dirty;
            const error = this.props.meta.error;
            return (
                <div className="form-group">
                    {this._renderLabel()}
                    {this._renderImagePreview(this.state.previewImage)}
                    <Dropzone
                        className="drop-zone"
                        {...this.props.input}
                        onDrop={(file) => {
                            this.setState({
                                fileName: file[0].name
                            });
                        }}>
                        {this._renderFileName(this.state.fileName)}
                    </Dropzone>

                    {this._renderError(touched, dirty, error)}
                </div>
            );
        }catch(error){
            console.error(error);
            return null;
        }
    }

    _renderCheckBox(){
        const touched = this.props.meta.touched;
        const dirty = this.props.meta.dirty;

        return (
            <div className="checkbox">
                <label>
                    <input
                        {...this.props.input}
                        type={this.props.type}/>
                        <span className="noselect">
                            {this.props.label.title}
                        </span>
                </label>
            </div>
        );
    }

    _renderTextArea(){
        const touched = this.props.meta.touched;
        const dirty = this.props.meta.dirty;
        const error = this.props.meta.error;

        const {input} = this.props;
        return (
            <div className="form-group">
                {this._renderLabel()}
                <textarea
                    {...this.props.input}
                    rows={4}
                    className="form-control"
                    autoFocus={this.props.focus}
                    type={this.props.type}>
                    {input.value}
                </textarea>
                {this._renderError(touched, dirty, error)}
            </div>
        );
    }

    imageUploadHanlde(fileList){
        const params = {
            title: fileList[0].name,
            table: this.props.table,
            attach: fileList,
            parent: parseInt(this.props.parent)
        }
        return Tools.apiCall(apiUrls['add'], {...params}, false).then((result) => {
            if(result.success){
                const attach = MEDIA_URL + result.data.attach;
                if(this.state.ReactSummernote){
                    const ReactSummernote = this.state.ReactSummernote.default;
                    ReactSummernote.insertImage(attach, image =>{
                        if(image.width() <= 400){
                            image.css('width', image.width());
                        }else{
                            image.css('width', '100%');
                        }
                    });
                }
            }
        });
    }

    _renderRichText(){
        const touched = this.props.meta.touched;
        const dirty = this.props.meta.dirty;
        const error = this.props.meta.error;
        const {input} = this.props;

        if(!this.state.ReactSummernote){
            return null;
        }
        const ReactSummernote = this.state.ReactSummernote.default;

        return (
            <div className="form-group">
                {this._renderLabel()}
                <ReactSummernote
                    {...this.props.input}
                    autoFocus={this.props.focus}
                    value={input.value}
                    onImageUpload={this.imageUploadHanlde}
                    options={{
                        maxHeight: 300,
                        dialogsInBody: true,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['fontname', ['fontname']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['fullscreen', 'codeview']]
                        ]
                    }}
                />
                {this._renderError(touched, dirty, error)}
            </div>
        );
    }

    _renderSelect(){
        const touched = this.props.meta.touched;
        const dirty = this.props.meta.dirty;
        const error = this.props.meta.error;

        const {input} = this.props;
        return (
            <div className="form-group">
                {this._renderLabel()}
                <Select
                    {...this.props.input}
                    disabled={this.props.disabled}
                    value={input.value}
                    valueKey="id"
                    labelKey="title"
                    options={this.props.options}
                    clearable={false}
                    // onChange={input.onChange}
                    onChange={value => this.handleSelectChange(value)}
                    onBlur={() => input.onBlur(input.value)}
                />
                {this._renderError(touched, dirty, error)}
            </div>
        )
    }

    _renderDate(){
        const touched = this.props.meta.touched;
        const dirty = this.props.meta.dirty;
        const error = this.props.meta.error;

        const {input} = this.props;
        return (
            <div className="form-group">
                {this._renderLabel()}
                <div>
                    <CustomDatePicker
                        {...this.props.input}
                        value={input.value}
                        onChange={this.handleInputChange}/>
                </div>
                {this._renderError(touched, dirty, error)}
            </div>
        );
    }

    _renderDateRange(){
        const touched = this.props.meta.touched;
        const dirty = this.props.meta.dirty;
        const error = this.props.meta.error;

        const {input} = this.props;
        return (
            <div className="form-group">
                {this._renderLabel()}
                <div>
                    <CustomDateRangePicker
                        {...this.props.input}
                        value={input.value}
                        onChange={this.handleInputChange}/>
                </div>
                {this._renderError(touched, dirty, error)}
            </div>
        );
    }

    _renderLabel(){
        if(!this.props.label || !this.props.label.title) return null;
        if(this.props.showLabel){
            return <label className={this.props.label.rules.required?'red-dot':''}>{this.props.label.title}</label>
        }
        return null;
    }

    render(){
        if(!this.props.show){
            return null
        }

        const {input} = this.props;

        switch(this.props.type){
            case 'text':
            case 'number':
            case 'email':
                return this._renderInput();
            case 'select':
                return this._renderSelect();
            case 'richtext':
                return this._renderRichText();
            case 'textarea':
                return this._renderTextArea();
            case 'checkbox':
                return this._renderCheckBox();
            case 'image':
                return this._renderImage();
            case 'file':
                return this._renderFile();
            case 'date':
                return this._renderDate();
            case 'dateRange':
                return this._renderDateRange();
            default:
                return this._renderInput();
        }
    }
}

export default FormInput;
