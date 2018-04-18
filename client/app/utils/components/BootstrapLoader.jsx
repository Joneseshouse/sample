export default () => {
    return new Promise(resolve => {
        require.ensure([], () => {
            require('libs/bootstrap/css/bootstrap.min.css');
            resolve({
            });
        });
    });
};