/*jquery 提示汉化*/
jQuery.extend(jQuery.validator.messages, {
        required: "必选字段",
    	remote: "请修正该字段",
    	email: "请输入正确格式的电子邮件",
    	url: "请输入合法的网址",
    	date: "请输入合法的日期",
    	dateISO: "请输入合法的日期 (ISO).",
    	number: "请输入合法的数字",
    	digits: "只能输入整数",
    	creditcard: "请输入合法的信用卡号",
    	equalTo: "请再次输入相同的值",
    	accept: "请输入拥有合法后缀名的字符串",
    	maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
    	minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
    	rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
    	range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
    	max: jQuery.validator.format("请输入一个最大为{0} 的值"),
    	min: jQuery.validator.format("请输入一个最小为{0} 的值")
    });
var tsfNum=0;
var pickID="";
var tsf_selector="";
function get_max_id(){
	var i = 0;
	var id="";
	var arr=new Array();
	var lastNum=0;
	$('group#cn_3dant_diy').children().each(function(){
		$(this).children().each(function(){
			id=$(this).attr("id");			
			arr[i]=id;
			i += 1;
		   });
	});
	if(arr.length===0){	
		return 0;
	}
	arr.sort();
	lastNum=Number(arr[arr.length-1].substr(1));
	return lastNum;
}
function show_model_list(){
	var i = 0;
	$("tr#title").nextAll("tr").remove();
	   $('group#cn_3dant_diy').children().each(function(){
		   $(this).children().each(function(){
			   var id=$(this).attr("id");
			   var description=$(this).attr("description");
			   var select="<button id='s"+id+"' type='button' class='btn btn-default btn-xs' aria-label='选择'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			   var deletemodel="<button id='d"+id+"' type='button' class='btn btn-default btn-xs' aria-label='删除'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			   var tag=$(this).attr("tag");
			   var str="<tr><td>"+id+"</td><td>"+description+"</td><td>"+select+"</td><td>"+deletemodel+"</td><td>"+tag+"</td></tr>";
			   $("tr#title").after(str);
			   $("button#s"+id).attr("onclick","list_select_model('"+id+"')");
			   $("button#d"+id).attr("onclick","list_delete_model('"+id+"')");			   
			   i += 1;
		   });		   
	   });
	   var endStr="<tr><td colspan='5'>共计"+i+"个模型</td></tr>";
	   $("tr#title").after(endStr);
}
function set_pickid(id){
	pickID=id;
	tsf_selector="transform#"+pickID;
}
function form_disable(){	
	$("[onchange='change()']").attr("disabled","disabled");
	$("input#radius").attr("disabled","disabled");
	$("shape#point_sphere").attr("render","false");
}
function form_enable(){
	$(":disabled").removeAttr("disabled");
	$("#point_sphere").attr("render","true");
}
function set_form(){	
	if(!pickID){		
		form_disable();	
		$(":disabled").val("");
		$(".info").text(pickID);
		return false;
	}
	form_enable();
	$(".info").text(pickID);
	var ts=$(tsf_selector).attr("translation").split(",");
	$("#ts_x").val(ts[0]);
	$("#ts_y").val(ts[1]);
	$("#ts_z").val(ts[2]);
	var rt=$(tsf_selector).attr("rotation").split(",");
	$("#rt_x").val(rt[0]);
	$("#rt_y").val(rt[1]);
	$("#rt_z").val(rt[2]);
	$("#rt_a").val(rt[3]);
	var sc=$(tsf_selector).attr("scale").split(",");
	$("#sc_x").val(sc[0]);
	$("#sc_y").val(sc[1]);
	$("#sc_z").val(sc[2]);
	if ($(tsf_selector).parent().attr("id")==="cn_3dant_basic"){
		var color=$(tsf_selector).find("material").attr("diffusecolor").split(",");	
		$("#color_r").val(color[0]);
		$("#color_g").val(color[1]);
		$("#color_b").val(color[2]);
	}	
	var pointRadius=$("transform#marker").attr("scale").split(",");
	$("input#radius").val(pointRadius[0]);
	var title=$(tsf_selector).attr("description");
	$("input#title").val(title);
}
function selectmodel(tsf){
	set_pickid($(tsf).attr("id"));
	var groupId=$(tsf).parent().attr("id");
	if(groupId==="cn_3dant_basic"){
		$("div#color").attr("class","form-group show");
	}else{
		$("div#color").attr("class","form-group hidden");
	}
	set_form();
}
function list_select_model(id){
	set_pickid(id);
	set_form();
	$('#modelListModal').modal('hide');
}
function change(){
	if(!pickID){
		alert("请选择模型后，再调整参数！");
		return false;
	}
	var dou=",";
	var ts_string=$("#ts_x").val()+dou+$("#ts_y").val()+dou+$("#ts_z").val();
	$(tsf_selector).attr("translation",ts_string);
	var rt_string=$("#rt_x").val()+dou+$("#rt_y").val()+dou+$("#rt_z").val()+dou+$("#rt_a").val();
	$(tsf_selector).attr("rotation",rt_string);
	var sc_string=$("#sc_x").val()+dou+$("#sc_y").val()+dou+$("#sc_z").val();
	$(tsf_selector).attr("scale",sc_string);
	var title=$("input#title").val();
	$(tsf_selector).attr("description",title);
	var color_string=$("#color_r").val()+dou+$("#color_g").val()+dou+$("#color_b").val();
	$(tsf_selector).find("material").attr("diffusecolor",color_string);
}
function change_point_sphere(){
	var radius=$("#radius").val();
	var sc=radius+","+radius+","+radius;
	$("#marker").attr("scale",sc);
}
function groupClick(event){
	var info=event.hitPnt;
	$('#marker').attr('translation', info);
	var x=Math.round(info[0]*1000)/1000;
	var y=Math.round(info[1]*1000)/1000;
	var z=Math.round(info[2]*1000)/1000;
	var xyz="X:"+x+"<br/>Y:"+y+"<br/>Z:"+z;
	$("#xyz").html(xyz);
}
function deletemodel(){
	if(!pickID){
		alert("请点击选择要删除的模型！");
		return false;
	}
	if (!confirm("确定删除模型"+pickID+"？")){
		return false;
	}	
	var result=$(tsf_selector).remove();	
	if(result.attr("id")==null){
		alert("模型"+pickID+"已不存在！");
		return false;
	}
	pickID="";
	set_form();
	//TODO:删除后变更表格状态
}
function list_delete_model(id){
	set_pickid(id);
	deletemodel();
	$('#modelListModal').modal('hide');
}
function emptymodel(){
	if(!confirm("确定移除所以模型？")){
		return false;
	}
	$("group#cn_3dant_shared").empty();
	$("group#cn_3dant_basic").empty();
	$("group#cn_3dant_internet").empty();
	tsfNum=0;
	pickID="";
	set_form();
}
function clearNoNum(obj) {
	obj.value = obj.value.replace(/[^\d.-]/g, ""); //清除“数字”和“.”以外的字符  
	obj.value = obj.value.replace(/^\./g, ""); //验证第一个字符是数字而不是. 
	obj.value = obj.value.replace(/\.{2,}/g, "."); //只保留第一个. 清除多余的.   
	obj.value = obj.value.replace(".", "$#$").replace(/\./g,"").replace("$#$",".");
}
function addmodel(lujing){
	  var daimaRegex=/^([a-fA-F0-9]{32})$/;
	  var daima=$("#daima").val();
	  if(!daimaRegex.test(daima)){			
			$("#daima").val("模型代码格式错误!!");
			return false;
		}
	  var str="<transform id='s"+tsfNum+"' tag='shared' description='未命名' onclick='selectmodel(this)' rotation='0,0,1,0' scale='1,1,1' translation='0,0,0'><Inline nameSpaceName='in' tag='cn_3dant_inline' mapDEFToID='true' url='"+lujing+"/"+daima+"/model.x3d'></Inline></transform>";
		$('group#cn_3dant_shared').append(str);	
		set_pickid("s"+tsfNum);				
		set_form();
		$("shape#point_sphere").attr("render","false");
		tsfNum +=1;
		$('#addModelModal').modal('hide');
}
function insert_shape(shape){
	function get_num(){
		var suiji = Math.random();
		var num = Math.round(suiji*1000)/1000;
		return num;
	}
	var color=get_num()+","+get_num()+","+get_num();
	var str="<transform id='s"+tsfNum+"' tag='basic' description='未命名' onclick='selectmodel(this)' rotation='0,0,1,0' scale='1,1,1' translation='0,0,0'><shape><appearance><material diffuseColor='"+color+"'> </material></appearance><"+shape+" id='g"+tsfNum+"'></"+shape+"></shape></transform>";
	$('group#cn_3dant_basic').append(str);
	if(shape==="text"){
		var str=prompt("请输入文字内容：（50字以内）","");
		if(str.length>50){
			alert("输入文字内容过长！");
			return false;
		}//TODO：如果为空
		$("text#g"+tsfNum).attr("string",str);
		$("text#g"+tsfNum).attr("solid","false");
	}
	set_pickid("s"+tsfNum);				
	set_form();
	$("shape#point_sphere").attr("render","false");
	tsfNum +=1;
	$('#addModelModal').modal('hide');
}
function change_background(self){
	var sreg = /_\d_/;
	var topurl=$("background").attr("topurl");
	var lefturl=$("background").attr("lefturl");
	var righturl=$("background").attr("righturl");
	var fronturl=$("background").attr("fronturl");
	var bottomurl=$("background").attr("bottomurl");
	var backurl=$("background").attr("backurl");
	var num="_"+self.value+"_";	
	topurl=topurl.replace(sreg,num);
	bottomurl=bottomurl.replace(sreg,num);
	fronturl=fronturl.replace(sreg,num);
	backurl=backurl.replace(sreg,num);
	righturl=righturl.replace(sreg,num);
	lefturl=lefturl.replace(sreg,num);
	$("background").attr("backurl",backurl);
	$("background").attr("fronturl",fronturl);
	$("background").attr("topurl",topurl);
	$("background").attr("bottomurl",bottomurl);
	$("background").attr("righturl",righturl);
	$("background").attr("lefturl",lefturl);
}
function screenShot(x3dID){
	var imgUrl = document
	.getElementById(x3dID).runtime
	.getScreenshot();
	return imgUrl;
}