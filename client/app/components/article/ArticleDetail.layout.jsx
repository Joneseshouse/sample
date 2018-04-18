import React from 'react';
import Link from 'react-router/lib/Link';
import values from 'lodash/values';
import isEmpty from 'lodash/isEmpty';
import {labels} from './_data';

import Tools from 'helpers/Tools';
import Paginator from 'utils/components/Paginator';

import store from 'app/store';

import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import WaitingMessage from 'utils/components/WaitingMessage';

import DetailForm from 'components/article/forms/Detail.form';


class ArticleDetailLayout extends React.Component {
	constructor(props) {
		super(props);
		this.state = {};
		this._renderContent = this._renderContent.bind(this);
	}

	_renderContent(){
		if(!this.props.dataLoaded){
			return <WaitingMessage/>
		}
		return (
			<div>
				<div className="breadcrumb-container">
					<Link to={Tools.toUrl('category', ['article'])}>Bài viết</Link>
					&nbsp;/&nbsp;
					<Link to={Tools.toUrl('article', [this.props.category.id])}>
						{this.props.category.title}
					</Link>
					&nbsp;/&nbsp;
					{this.props.title}
				</div>
				<div className="main-content padding-10">
					<DetailForm
						onSubmit={this.props.onChange}
						labels={labels.detailForm}
						params={this.props.params}
						submitTitle="Save">
						<button
							type="button"
							className="btn btn-warning cancel"
							onClick={() => Tools.goToUrl('article', [store.getState().articleReducer.obj.category_id])}>
							<span className="glyphicon glyphicon-chevron-left"></span> &nbsp;
							Back
						</button>
					</DetailForm>
				</div>
			</div>
		);
	}

	render() {
		return (
			<NavWrapper data-location={this.props.location} data-user={this.props.authReducer}>
				<div>
					{this._renderContent()}
				</div>
			</NavWrapper>
		);
	}
}

export default ArticleDetailLayout;
