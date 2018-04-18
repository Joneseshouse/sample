<!DOCTYPE html>
<html>
<head>
	<title>Grabbing receiver</title>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/1.5.1/fingerprint2.min.js"></script>
	<script type="text/javascript">

		var APP_NAMESPACE = '24horder';
		var mainKey = APP_NAMESPACE + '_admin_grabbingItems';
		window.addEventListener("message", function(e){
		    var parent = window.parent;
		    var payload = JSON.parse(e.data);
		    if(payload.method === 'post'){
			    var newItem = payload.data;
			    var listItem = JSON.parse(localStorage[mainKey] || '[]');
			    // Math purchase code and bill of landing code and properties
		    	var matchIndex = _.findIndex(
		    		listItem,
		    		{
		    			url: newItem.purchase_code,
		    			properties: newItem.bill_of_landing_code
		    		}
		    	);
		    	if(matchIndex === -1){
		    		listItem.push(newItem);
		    	}
		    	window.localStorage[mainKey] = JSON.stringify(listItem);
		    }else if(payload.method === 'get'){
		    	if(payload.key === 'authData'){
		    		new Fingerprint2().get(function(fingerprint, components){
						var result =  JSON.parse(window.localStorage.getItem(APP_NAMESPACE + "_admin_authData"));
		    			result.fingerprint = fingerprint;
		    			console.log('-----------------------');
		    			console.log(result);
					    parent.postMessage(result, e.origin);
					});
				};
		    }
		}, false);
	</script>
</head>
<body>Item receiver</body>
</html>