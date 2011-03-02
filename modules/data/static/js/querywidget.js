var criteriaNum = 0;
var allCriteria = new Array();
var criteriaRoot = 0;

function encodeRequestData(key, value){
	return escape(key) + "=" + value;
}

function displayPageInfo(currPage, totalPages, pageSize, numPageProducts, totalTypeProducts){
	pageInfo = '<div class="pp_pageLinks">';
	if(numPageProducts > 0){
		prodRangeStart = (currPage - 1) * pageSize + 1;
		prodRangeEnd = (currPage - 1) * pageSize + numPageProducts;
		pageInfo += 'Page ' + currPage + ' of ' + totalPages + '&nbsp;';
		pageInfo += '(Products ' + prodRangeStart + ' to ' + prodRangeEnd;
		if(totalTypeProducts > 0){
			pageInfo += ', filtered from ' + totalTypeProducts;
		}
		pageInfo += ')&nbsp;&nbsp;';
		if(currPage > 1){
			pageInfo += '<a href="#" onclick="getPrevPage()">&lt;&lt;&nbsp;Previous Page</a>&nbsp;&nbsp;';
		}
		if(currPage < totalPages){
			pageInfo += '<a href="#" onclick="getNextPage()">Next Page&nbsp;&gt;&gt;</a>';
		}
	}else{
		pageInfo += 'No products meet given criteria.';
	}
	pageInfo += '</div>';
	$("#" + htmlID).prepend(pageInfo);
}

function getNextPage(){
	currPage = parseInt($("#page_num").val());
	$("#page_num").val(currPage + 1);
	sendRequest("html");
}

function getPrevPage(){
	currPage = parseInt($("#page_num").val());
	$("#page_num").val(currPage - 1);
	sendRequest("html");
}

function formatCriteria(index){
	if(allCriteria[index].type == 'term'){
		requestData = encodeRequestData("Criteria[" + index + "][CriteriaType]", "Term");
		requestData += '&' + encodeRequestData("Criteria[" + index + "][ElementName]", allCriteria[index].element);
		requestData += '&' + encodeRequestData("Criteria[" + index + "][Value]", allCriteria[index].value);
	}else if(allCriteria[index].type == 'range'){
		requestData = encodeRequestData("Criteria[" + index + "][CriteriaType]", "Range");
		requestData += '&' + encodeRequestData("Criteria[" + index + "][ElementName]", allCriteria[index].element);
		requestData += '&' + encodeRequestData("Criteria[" + index + "][Min]", allCriteria[index].min);
		requestData += '&' + encodeRequestData("Criteria[" + index + "][Max]", allCriteria[index].max);
	}else if(allCriteria[index].type == 'boolean'){
		requestData = encodeRequestData("Criteria[" + index + "][CriteriaType]", "Boolean");
		requestData += '&' + encodeRequestData("Criteria[" + index + "][Operator]", allCriteria[index].operator);
		for(i = 0; i < allCriteria[index].criteria.length; i++){
			requestData += '&' + encodeRequestData("Criteria[" + index + "][CriteriaTerms][" + i + "]", allCriteria[index].criteria[i]);
		}
		for(i = 0; i < allCriteria[index].criteria.length; i++){
			requestData +=  '&' + formatCriteria(allCriteria[index].criteria[i]);
		}
	}
	return requestData;
}

function formatQueryRequest(expectedType){
	requestData = encodeRequestData("Types[0]", ptName);
	requestData += '&' + encodeRequestData("PagedResults", "1");
	requestData += '&' + encodeRequestData("PageNum", $("#page_num").val());
	requestData += '&' + encodeRequestData("RootIndex", criteriaRoot);
	requestData += '&' + encodeRequestData("OutputFormat", expectedType);
	requestData += '&' + formatCriteria(criteriaRoot);
	$.post(siteUrl + "/queryScript.do",
		requestData,
		function(data){
			if(expectedType == "html"){
				$("#" + htmlID).html(data);
				currPage = parseInt($("#page_num").val());
				totalPages = parseInt($("#total_pages").val());
				pageSize = parseInt($("#page_size").val());
				numPageProducts = $("#product_list > li").length;
				totalTypeProducts = $("#total_type_products").val();
				displayPageInfo(currPage, totalPages, pageSize, numPageProducts, totalTypeProducts);
			}else if(expectedType == "json"){
				renderJsonOutput(data);
				currPage = parseInt($("#page_num").val());
				totalPages = data['totalPages'];
				pageSize = data['pageSize'];
				numPageProducts = data['productList'].length;
				totalTypeProducts = data['totalTypeProducts'];
				displayPageInfo(currPage, totalPages, pageSize, numPageProducts, totalTypeProducts);
			}
		},
		expectedType);
}

function formatProductPageRequest(expectedType){
	requestData = encodeRequestData("Type", ptName);
	requestData += '&' + encodeRequestData("PageNum", $("#page_num").val());
	requestData += '&' + encodeRequestData("OutputFormat", expectedType);
	$.post(siteUrl + "/pageScript.do", 
		requestData, 
		function(data){
			if(expectedType == "html"){
				$("#" + htmlID).html(data);
				currPage = parseInt($("#page_num").val());
				totalPages = parseInt($("#total_pages").val());
				pageSize = parseInt($("#page_size").val());
				numPageProducts = $("#product_list > li").length;
				totalTypeProducts = 0;
				displayPageInfo(currPage, totalPages, pageSize, numPageProducts, totalTypeProducts);
			}else if(expectedType == "json"){
				renderJsonOutput(data);
				currPage = parseInt($("#page_num").val());
				totalPages = data['totalPages'];
				pageSize = data['pageSize'];
				numPageProducts = data['productList'].length;
				totalTypeProducts = 0;
				displayPageInfo(currPage, totalPages, pageSize, numPageProducts, totalTypeProducts);
			}
		},
		expectedType);
}

// Determine whether to request a page or query, depending upon the presence
// of any non-boolean criteria.  Returns 0 for a page and 1 for a query.
function determineRequest(index){
	if(allCriteria[index].type != 'boolean'){
		return 1;
	}
	for(i = 0; i < allCriteria[index].criteria.length; i++){
		if(determineRequest(allCriteria[index].criteria[i])){
			return 1;
		}
	}
	return 0;
}

function sendRequest(expectedType){
	if(determineRequest(criteriaRoot)){
		formatQueryRequest(expectedType);
	}else{
		formatProductPageRequest(expectedType);
	}
}

function addCriteria(){
	index = criteriaNum;
	criteriaNum++;
	allCriteria[index] = new Object();
	return index;
}

function addTermCriteria(elementName, value, parentIndex){
	index = addCriteria();
	allCriteria[index].type = 'term';
	allCriteria[index].parentIndex = parentIndex;
	allCriteria[index].element = elementName;
	allCriteria[index].value = value;
	if(parentIndex != null){
		allCriteria[parentIndex].criteria.push(index);
	}
	return index;
}

function addRangeCriteria(elementName, min, max, parentIndex){
	index = addCriteria();
	allCriteria[index].type = 'range';
	allCriteria[index].parentIndex = parentIndex;
	allCriteria[index].element = elementName;
	allCriteria[index].min = min;
	allCriteria[index].max = max;
	if(parentIndex != null){
		allCriteria[parentIndex].criteria.push(index);
	}
	return index;
}

function addBooleanCriteria(operator, parentIndex){
	index = addCriteria();
	allCriteria[index].type = 'boolean';
	allCriteria[index].parentIndex = parentIndex;
	allCriteria[index].operator = operator;
	allCriteria[index].criteria = new Array();
	if(parentIndex != null){
		allCriteria[parentIndex].criteria.push(allCriteria[index]);
	}
	return index;
}

function removeCriteria(index){
	if(allCriteria[index].parentIndex != null){
		parentCriteria = allCriteria[allCriteria[index].parentIndex];
		for(i = 0; i < parentCriteria.criteria.length; i++){
			if(parentCriteria.criteria[i] == index){
				parentCriteria.criteria.splice(i, 1);
				i = parentCriteria.criteria.length;
			}
		}
	}
	allCriteria[index] = null;
}
