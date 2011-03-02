function addFilter(){
	key = $("#filterKey").val();
	value = $("#filterValue").val();
	$("#page_num").val(1);
	if(value!=""){
		if(!allCriteria[criteriaRoot]){
			criteriaRoot = addBooleanCriteria('and', null);
		}
		index = addTermCriteria(key, value, criteriaRoot);
		var filterText = '<tr id="filter' + index + '">';
		filterText += '<td>' + key + '</td><td>=</td><td>' + value + '</td>';
		filterText += '<td align="right">';
		filterText += '<input type="button" value="Remove" onclick="removeFilter(\'' + index + '\')" />';
		filterText += '</td></tr>';
		$("#filters").append(filterText);
		sendRequest(resultFormat);
		$("#filterValue").val("");
    }
}

function removeFilter(filterIndex){
	$("#page_num").val(1);
	$("#filter" + filterIndex).remove();
	removeCriteria(filterIndex);
	sendRequest(resultFormat);
}

function renderJsonOutput(data){
	output = '<ul class="pp_productList" id="product_list">';
	for(i = 0; i < data['productList'].length; i++){
		output += '<li><a href="' + siteUrl + '/product/' + data['productList'][i]['id'] + '">';
		output += data['productList'][i]['name'] + '</li>';
	}
	output += '</ul>';
	output += '<input type="hidden" id="total_pages" value="' + data['totalPages'] + '">';
	output += '<input type="hidden" id="page_size" value="' + data['pageSize'] + '">';
	output += '<input type="hidden" id="total_type_products" value="' + data['totalTypeProducts'] + '">';
	$("#" + htmlID).html(output);
}