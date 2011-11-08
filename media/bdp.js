/**
 * 
 */

function updateInnerHtml(xmlreply, td_status) {
	if (xmlreply.status == Http.Status.OK) {
		td_status.innerHTML = xmlreply.responseText;
	} else {
		alert('Cannot handle the AJAX call.');
	}
}

function asyncInnerHtmlFromUrl(url,element){
	Http.get({		
		url: url, 
		callback: updateInnerHtml,
		cache: Http.Cache.Get
	}, [element]);	
}

function report_img_broken(img){
	var img_with_error = img.src;
	img.src = try_smaller_biltema(img);
	//img.src = '/bdp.google/media/bdp_default_image_200_200.jpg';
	var w_url = '/index/ajax-html/cmd/Error/type/broken_link/text/'+img_with_error.replace('/','_').replace(':','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_').replace('/','_');
	Http.get({
		url: w_url ,
		//callback: alert(),
		cache: Http.Cache.Get
	}, null);
}

function try_smaller_biltema(img){
	var img_src_default = '/media/bdp_default_image_200_200.jpg';
	// if contains biltema
	// "http://www.biltema.dk/ProductImages/75/large/75-934_l.jpg"
	// to "http://www.biltema.dk/ProductImages/75/medium/75-934_m.jpg"
	// to "http://www.biltema.dk/ProductImages/75/small/75-934_s.jpg"
	// if contains large
	//     replace _l.jpg with _m.jpg and replace large with medium 
	// and return
	// if contains medium
	//     replace _l.jpg with _m.jpg and replace large with medium 
	// and return
	// if contains small
	//     set 
	// and return
	return img_src_default;
}

