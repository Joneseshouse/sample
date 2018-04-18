export default () => {
    return new Promise(resolve => {
        require.ensure([], () => {
			window.jQuery = require('jquery');
            require('react-summernote/dist/react-summernote.css');
			require('libs/bootstrap/js/bootstrap.min.js');
            resolve({
                ReactSummernote: require('react-summernote')
            });
        });
    });
};