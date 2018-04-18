import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import store from 'app/store';
import * as actionCreators from 'app/actions/actionCreators';

import {labels} from '../_data';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';
import {APP, FIELD_TYPE } from 'app/constants';

import Tools from 'helpers/Tools';

class DetailForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {}
    }

    componentDidMount(){
    }

    render() {
        const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
        return (
            <form
                onSubmit={handleSubmit(onSubmit)}>

                <Field
                    name="title"
                    type="text"
                    focus={true}
                    component={FormInput}
                    label={this.props.labels.title}/>
                <Field
                    name="slug"
                    type="text"
                    component={FormInput}
                    label={this.props.labels.slug}/>
                <Field
                    name="thumbnail"
                    type="image"
                    component={FormInput}
                    label={this.props.labels.thumbnail}/>
                <Field
                    name="content"
                    type="richtext"
                    table="articles"
                    parent={this.props.params.id}
                    //loader={() => import('react-summernote')}
                    component={FormInput}
                    label={this.props.labels.content}/>
                <Field
                    name="order"
                    type="number"
                    component={FormInput}
                    label={this.props.labels.order}/>

                {error && <div className="alert alert-danger" role="alert">{error}</div>}

                <div className="row custom-modal-footer">
                    <div className="col-md-6 cancel">
                        {this.props.children}
                    </div>
                    <div className="col-md-6 submit">
                        <button className="btn btn-success" disabled={submitting}>
                            <span className="glyphicon glyphicon-ok"></span> &nbsp;
                            {this.props.submitTitle}
                        </button>
                    </div>
                </div>
            </form>
        );
    }
}

const validate = values => {
    return ValidateTools.validateInput(
        values,
        Tools.getRules(labels.mainForm)
    );
};

function mapStateToProps(state){
    return {
        initialValues: state.articleReducer.obj
    }
}

function mapDispatchToProps(dispatch){
    return bindActionCreators(actionCreators, dispatch);
}

DetailForm.propTypes = {
};

DetailForm.defaultProps = {
};

// Decorate the form component
const form = reduxForm({
    form: 'ArticleDetailForm', // a unique name for this form
    enableReinitialize: true,
    validate
})(DetailForm);

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(form);

// export default DetailForm;
