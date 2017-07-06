$(function($){
 
	var url=WebServiceURL+"notice_cont";
	var studentNo=localStorage['studentNo'];
	//console.log(url);
	$.ajax({
		url:url,
		type:'POST',
		dataType:'jsonp',
		success:function(data){
			if(2000==data['code']&&'success'==data['msg']){
			var noticeData= data["data"];
			for(var i=0;i<noticeData.length;i++)
				addList(noticeData[i],i);
			}else{
				document.write("�����ݣ�");
			}
		}
	});
	var addList=function(notice,index){
		console.log(notice);
		var href="notice_detail.html?noticeNo="+notice["noticeNo"];
		var contextHead="<a class=\"list\" href=\""+href+"\">";
		var contextFoot="</a>";
		var part1="<div class=\"list_img\"><img src=\""+ServiceURL+"attachments"+notice["thumb"]+"\"></div>";
		var part2="<div class=\"list_datial\"><div class=\"list_tit\">"+notice["noticeTitle"]+"</div>";
		var part3="<div class=\"list_cont\">"+notice["noticeContent"]+"</div></div>";
		context=contextHead+part1+part2+part3+contextFoot;
		$('#notice_list').append(context);
	}
			/*
				<a class="list">
					<div class="list_img"><img src="images/123.jpg"/></div>
					<div class="list_detial">
						<div class="list_tit">С�ף�����Ӫ�����ߵ���ͷ</div>
						<div class="list_cont">���һ��ʱ�䣬С������Щ�����£����鲢����󣬵�ȴֱ��ʹ�㡣����Դ��һƪ��ţ������������������...</div>
					</div>
				</a>
			*/
});