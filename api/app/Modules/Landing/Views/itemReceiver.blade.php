<!DOCTYPE html>
<html>
<head>
	<title>Item receiver</title>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
	<script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
	<script type="text/javascript" src="https://momentjs.com/downloads/moment-timezone-with-data.min.js"></script>
	<script type="text/javascript">
		var APP_NAMESPACE = '24horder';
		var mainKey = APP_NAMESPACE + '_user_orderItems';
		window.addEventListener("message", function(e){
		    var parent = window.parent;
		    var payload = JSON.parse(e.data);
		    if(payload.method === 'post'){
			    console.log(payload);
			    var listNewItem = payload.data;
			    var listItem = JSON.parse(localStorage[mainKey] || '[]');
			    // Math url and properties
			    _.forEach(listNewItem, function(value){
			    	var matchIndex = _.findIndex(listItem, {url: value.url, properties: value.properties});
			    	if(matchIndex !== -1){
			    		// Match -> Increase quantity
			    		listItem[matchIndex].created_at = moment().tz('Asia/Ho_Chi_Minh').format().split('T')[0];
			    		listItem[matchIndex].quantity = parseInt(listItem[matchIndex].quantity) + parseInt(value.quantity);
			    	}else{
			    		// Not Match -> Insert
			    		value.created_at = moment().tz('Asia/Ho_Chi_Minh').format().split('T')[0];
			    		listItem.push(value);
			    	}
			    });
		    	window.localStorage[mainKey] = JSON.stringify(listItem);
		    }
		}, false);
	</script>
</head>
<body>Item receiver</body>
</html>