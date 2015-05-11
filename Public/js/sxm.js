var tsfNum=0;//最大ID+1,使用后+1
var pickID="";//选择的transform的id
var pickNum=0;//选择的transform的id数字
var last_pick_id="";//上一次选择的transform的ID
var tsf_selector="";//transform#pickID
var pickInfo;//点击位置信息
var pos;
var rot;
var x3d=new Array();
var recover_time=0;
/**
 * 通过线球旋转物体，并侧边栏旋转参数，内部调用
 */
var viewFunc = function(evt) {
	pos = evt.position;
	rot = evt.orientation;
	mat = evt.matrix;
	x=Math.round(rot[0].x*1000)/1000;
	y=Math.round(rot[0].y*1000)/1000;
	z=Math.round(rot[0].z*1000)/1000;
	a=Math.round(rot[1]*1000)/1000;
	// document.getElementById('coordinateAxesViewpoint').setAttribute(
	// 'position', pos.x+' '+pos.y+' '+pos.z);
	$("#" + pickID).attr('position', 0 + ',' + 0 + ',' + 0);
	$("#" + pickID).attr('rotation', x + ',' + y + ',' + z + ',' + a);
	set_form();	
	// x3dom.debug.logInfo('position: ' + pos.x+' '+pos.y+' '+pos.z +'\n' +
	// 'orientation: ' + rot[0].x+' '+rot[0].y+' '+rot[0].z+' '+rot[1]);
};
/**
 * 通过调整视角旋转坐标指针。
 * @param viewpointId 视角ID
 * @param transformId 坐标transform id
 */
function rotate(viewpointId,transformId){
	var viewFunc2 = function(evt) {
		pos = evt.position;
		rot = evt.orientation;
		mat = evt.matrix;
		$("#"+transformId).attr('position', 0 + ',' + 0 + ',' + 0);
		$("#"+transformId).attr('rotation', rot[0].x + ',' + rot[0].y + ',' + rot[0].z + ',' + rot[1]);
	}
	document.getElementById(viewpointId).addEventListener('viewpointChanged', viewFunc2, true);
}
/**
 * 获取载入文档最大transform ID 末尾数
 * @returns {Number} ID最大数
 */
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
/**
 * 显示已插入模型列表，清空原来模型列表，重写
 */
function show_model_list(){
	var i = 0;
	$("tr#title").nextAll("tr").remove();
	   $('group#cn_3dant_diy').children().each(function(){
		   $(this).children().each(function(){
			   var id=$(this).attr("id");
			   var description=$(this).attr("description");
			   var select="<button id='select"+id+"' type='button' class='btn btn-default btn-xs' aria-label='选择'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			   var delete_model="<button id='delete"+id+"' type='button' class='btn btn-default btn-xs' aria-label='删除'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			   var show_model="<button id='show"+id+"' type='button' class='btn btn-default btn-xs' aria-label='查看'><span class='glyphicon glyphicon-zoom-in' aria-hidden='true'></span></button>";
			   var tag=$(this).attr("tag");
			   var str="<tr><td>"+id+"</td><td>"+description+"</td><td>"+select+"</td><td>"+delete_model+"</td><td>"+show_model+"</td><td>"+tag+"</td></tr>";
			   $("tr#title").after(str);
			   $("button#select"+id).attr("onclick","list_select_model('"+id+"')");
			   $("button#delete"+id).attr("onclick","list_delete_model('"+id+"')");
			   $("button#show"+id).attr("onclick","list_show_model('"+id+"')");
			   i += 1;
		   });		   
	   });
	   var endStr="<tr><td colspan='6'>共计"+i+"个模型</td></tr>";
	   $("tr#title").after(endStr);
}
/**
 * 显示已保存视角列表，清空原来列表，重写
 */
function show_view_list(){
	var i = 0;
	$("tr#view_title").nextAll("tr").remove();
	   $('#mainScene').children('viewpoint').each(function(){		   
			   var id=$(this).attr("id");
			   var description=$(this).attr("description")?$(this).attr("description"):"暂无";			   
			   if(id=="v"){
				  var delete_view="默认";
			   }else{
				   var delete_view="<button id='view_delete"+id+"' type='button' class='btn btn-default btn-xs' aria-label='删除'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			   }			   
			   var show_view="<button id='view_show"+id+"' type='button' class='btn btn-default btn-xs' aria-label='查看'><span class='glyphicon glyphicon-zoom-in' aria-hidden='true'></span></button>";
			   var str="<tr><td>"+id+"</td><td>"+description+"</td><td>"+delete_view+"</td><td>"+show_view+"</td></tr>";
			   $("tr#view_title").after(str);
			   $("button#view_delete"+id).attr("onclick","list_delete_view('"+id+"')");
			   $("button#view_show"+id).attr("onclick","list_show_view('"+id+"')");
			   i += 1;		  	   
	   });
	   var endStr="<tr><td colspan='4'>共计"+i+"个视角</td></tr>";
	   $("tr#view_title").after(endStr);
}
/**
 * 删除指定视角，默认视角v，无法删除。
 * @param viewID 视角id
 */
function list_delete_view(viewID){
	if(viewID=="v")return;
	cache();//缓存页面内容
	$("viewpoint#"+viewID).remove();
	$('#viewListModal').modal('hide');
}
function list_show_view(viewID){
	$("viewpoint#"+viewID).attr("set_bind","true");
	$('#viewListModal').modal('hide');
}
/**
 * 根据最新选择的transform ID ，更新相关数据
 * @param id
 */
function set_pickid(id){
	last_pick_id=pickID;//更新前一次选择
	pickID=id;
	pickNum=parseInt(pickID.substr(1));
	tsf_selector="transform#"+pickID;
	document.getElementById("coordinateAxesViewpoint").addEventListener('viewpointChanged', viewFunc, true);//更新旋转球，旋转对象
	set_form();
}
/**
 * 无选择时，禁用表格，隐藏点球
 */
function form_disable(){	
	$("[onchange='change()']").attr("disabled","disabled");
	$("input#radius").attr("disabled","disabled");
	$("shape#point_sphere").attr("render","false");
}
/**
 * 有选择对象是启用表格，显示点球
 */
function form_enable(){
	$(":disabled").removeAttr("disabled");
	$("#point_sphere").attr("render","true");
}
/**
 * 根据选择对象，设置表格显示内容。
 * @returns {Boolean} 无选择对象是返回false
 */
function set_form(){
	if(!pickID){		
		form_disable();	
		$(":disabled").val("");
		$(".info").text(pickID);
		return false;
	}
	//$(tsf_selector)[0].highlight(true,'1 0 0');选择高亮显示
	form_enable();
	$(".info").html(pickID);
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
	if($(tsf_selector).find("torus").length){
		var torus1 = $(tsf_selector).find("torus").attr("innerradius");
		var torus2 = $(tsf_selector).find("torus").attr("outerradius");
		$("input#torus1").val(torus1);
		$("input#torus2").val(torus2);
		$("div#torus").attr("class","form-group show");
	}else{
		$("div#torus").attr("class","form-group hidden");
	}
	var groupId=$(tsf_selector).parent().attr("id");
	if(groupId==="cn_3dant_basic"){
		$("div#color").attr("class","form-group show");
	}else{
		$("div#color").attr("class","form-group hidden");
	}
	var pointRadius=$("transform#marker").attr("scale").split(",");
	$("input#radius").val(pointRadius[0]);
	var title=$(tsf_selector).attr("description");
	$("input#title").val(title);
}
/**
 * 通过transform自身选择模型
 * @param tsf this
 */
function selectmodel(tsf_this){
	set_pickid($(tsf_this).attr("id"));	
	set_form();
}
/**
 * 通过列表ID选择模型，隐藏列表模态块
 * @param id 列表传递的ID
 */
function list_select_model(id){
	set_pickid(id);
	set_form();
	$('#modelListModal').modal('hide');
}
/**
 * 通过列表显示当前选择模型
 * @param id 列表传递的模型id
 * @return 列表模态块隐藏
 */
function list_show_model(id){
	set_pickid(id);
	set_form();
	showObject();
	$('#modelListModal').modal('hide');
}
/**
 * 表格数据改变，更新文档内容。
 * @returns {Boolean} 没有选择模型返回false
 *TODO: 区别更新内容，更新对应文档内容
 */
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
	var torus1=$("input#torus1").val();
	$(tsf_selector).find("torus").attr("innerradius",torus1);
	var torus2=$("input#torus2").val();
	$(tsf_selector).find("torus").attr("outerradius",torus2);
}
/**
 * 更新点球大小
 */
function change_point_sphere(){
	var radius=$("#radius").val();
	var sc=radius+","+radius+","+radius;
	$("#marker").attr("scale",sc);
}
/**
 * group点击事件
 * @param event
 * @return 显示点击位置坐标
 */
function groupClick(event){
	pickInfo=event.hitPnt;
	$('#marker').attr('translation', pickInfo);
	var x=Math.round(pickInfo[0]*1000)/1000;
	var y=Math.round(pickInfo[1]*1000)/1000;
	var z=Math.round(pickInfo[2]*1000)/1000;
	pickInfo=x+','+y+','+z;
	var xyz="X:"+x+"<br/>Y:"+y+"<br/>Z:"+z;
	$("#xyz").html(xyz);
}
/**
 * 删除选择的模型
 * @returns {Boolean} 选择错误是报警
 * @return pickID设置为空，更新表格。
 */
function deletemodel(){
	if(!pickID){
		alert("请点击选择要删除的模型！");
		return false;
	}
	if (!confirm("确定删除模型"+pickID+"？")){
		return false;
	}
	cache();//缓存页面内容
	var result=$(tsf_selector).remove();	
	if(result.attr("id")==null){
		alert("模型"+pickID+"已不存在！");
		return false;
	}
	pickID="";
	set_form();
}
/**
 * 移动模型，先点击要移动的模型，再点击目标位置
 * @returns {Boolean} 如果没有pickID 和 last_pick_id报错 返回false
 */
function moveModel(){
	if(!last_pick_id){
		alert("请点击选择要移动的模型和位置！");
		return false;
	}
	cache();//缓存页面内容
	$("#"+last_pick_id).attr("translation",pickInfo);
}
/**
 * 对齐模型，先点要移动的模型，再点击目标物体，分别对齐xyz
 * @returns {Boolean} 选择错误报错，返回false
 * @return 对齐模态块隐藏
 */
function alignModel(){	
	if(!last_pick_id){
		alert("请先后点击选择要对齐的模型！");
		$('#alignModelModal').modal('hide');
		return false;
	}
	if(last_pick_id===pickID){
		alert("请先后点击选择要对齐的两个不同的模型！");
		$('#alignModelModal').modal('hide');
		return false;
	}
	cache();//缓存页面内容
	var transform1 = $("#"+pickID).attr("translation").split(",");
	var transform2 = $("#"+last_pick_id).attr("translation").split(",");
	if($("input#align_x").is(":checked"))transform2[0]=transform1[0];
	if($("input#align_y").is(":checked"))transform2[1]=transform1[1];
	if($("input#align_z").is(":checked"))transform2[2]=transform1[2];
	$("#"+last_pick_id).attr("translation",transform2.join(","));
	$('#alignModelModal').modal('hide');
}
/**
 * 从列表删除模型，设置pickID 调用删除模型函数
 * @param id 列表传递ID
 * @return 列表模态块隐藏
 */
function list_delete_model(id){
	cache();//缓存页面内容
	set_pickid(id);
	deletemodel();
	$('#modelListModal').modal('hide');
}
/**
 * 清空当前diy所有模型
 * @param 确认框确认值
 * @returns {Boolean} 确认框取消返回false
 * 最大数设为0 
 */
function emptymodel(){
	if(!confirm("确定移除所以模型？")){
		return false;
	}
	cache();//缓存页面内容
	$("group#cn_3dant_shared").empty();
	$("group#cn_3dant_basic").empty();
	$("group#cn_3dant_internet").empty();
	$("viewpoint#v").nextAll('viewpoint').remove();
	tsfNum=0;
	pickID="";
	set_form();
}
/**
 * 清除非数字字符
 * @param obj 输入框本身
 */
function clearNoNum(obj) {
	obj.value = obj.value.replace(/[^\d.-]/g, ""); //清除“数字”和“.”以外的字符  
	obj.value = obj.value.replace(/^\./g, ""); //验证第一个字符是数字而不是. 
	obj.value = obj.value.replace(/\.{2,}/g, "."); //只保留第一个. 清除多余的.   
	obj.value = obj.value.replace(".", "$#$").replace(/\./g,"").replace("$#$",".");
}
/**
 * 添加站内分享的模型，实际添加transform 及子节点
 * @param lujing 模型上传路径upload /Public/upload/...
 * @param 输入模型代码，直接通过jquery获取
 * @returns {Boolean}
 */
function addmodel(lujing){
	  var daimaRegex=/^([a-fA-F0-9]{32})$/;
	  var daima=$("#daima").val();
	  if(!daimaRegex.test(daima)){			
			$("#daima").val("模型代码格式错误!!");
			return false;
		}
	  var str="<transform id='s"+tsfNum+"' tag='shared' description='未命名' onMouseDown='selectmodel(this)' rotation='0,0,1,0' scale='1,1,1' translation='0,0,0'><Inline nameSpaceName='in' tag='cn_3dant_inline' mapDEFToID='true' url='"+lujing+"/"+daima+"/model.x3d'></Inline></transform>";
		$('group#cn_3dant_shared').append(str);	
		set_pickid("s"+tsfNum);				
		set_form();
		$("shape#point_sphere").attr("render","false");
		tsfNum +=1;
		$('#addModelModal').modal('hide');
}
/**
 * 添加基本模型
 * @param shape 基本模型种类 box Sphere cylinder...
 * @returns {Boolean}
 */
function insert_shape(shape){
	var solid="true";
	var radius="";
	if(shape==="plane")solid="false";
	if(shape==="torus")radius="innerradius='0.5' outerradius='1'";
	function get_num(){
		var suiji = Math.random();
		var num = Math.round(suiji*1000)/1000;
		return num;
	}
	var color=get_num()+","+get_num()+","+get_num();
	var str="<transform id='s"+tsfNum+"' tag='basic' description='未命名' onMouseDown='selectmodel(this)' rotation='0,0,1,0' scale='1,1,1' translation='0,0,0'><shape><appearance><material diffuseColor='"+color+"'> </material></appearance><"+shape+" id='g"+tsfNum+"' "+radius+" solid='"+solid+"'></"+shape+"></shape></transform>";
	$('group#cn_3dant_basic').append(str);
	if(shape==="text"){//如果基本模型为文字，弹出输入框
		var str=prompt("请输入文字内容：（50字以内）","");
		if(str.length>50){//如果字符过长
			alert("输入文字内容过长！");
			return false;
		}
		if(str.length===0){//如果为空
			alert("请输入文字内容！");
			return false;
		}
		$("text#g"+tsfNum).attr("string",str);
		$("text#g"+tsfNum).attr("solid","false");
	}
	set_pickid("s"+tsfNum);
	set_form();
	$("shape#point_sphere").attr("render","false");
	tsfNum +=1;
	if(shape==="torus")$(tsf_selector).find("torus").attr({//如果基本模型为救生圈，要重写默认值
		"innerradius":"0.5",
		"outerradius":"1"
	});	
	$('#addModelModal').modal('hide');
}
/**
 * 复制当前选择模型
 * 
 */
function copyModel(){	
		var newID="s"+tsfNum;				
		var newScale=$(tsf_selector).attr('scale');		
		var newRotation=$(tsf_selector).attr('rotation');		
		var newTranslation=$(tsf_selector).attr('translation');		
		var xyz=newTranslation.split(',');		
		var x=parseInt(xyz[0])+1;
		var y=parseInt(xyz[1])+1;
		var z=parseInt(xyz[2])+1;
		newTranslation=x+','+y+','+z;
		var newTag=$(tsf_selector).attr('tag');	
		var newDescription=$(tsf_selector).attr('description')+"_副本";	
		var newNode=$(tsf_selector).html();
		var res=$(tsf_selector).after("<transform id="+newID+" onmousedown='selectmodel(this)'>"+newNode+"</transform>");
		$("transform#"+newID).attr('scale',newScale);
		$("transform#"+newID).attr('rotation',newRotation);
		$("transform#"+newID).attr('translation',newTranslation);
		$("transform#"+newID).attr('tag',newTag);
		$("transform#"+newID).attr('description',newDescription);
		set_pickid(newID);
		tsfNum += 1 ;
}
/**
 * 克隆当前选择模型 USE="def"

function cloneModel(){
	var mainNode=$(tsf_selector).children()[0];
	var tagName=mainNode.tagName;	
	if(!mainNode.getAttribute('USE')){
		var DEF="USED_"+pickID
		mainNode.setAttribute("DEF",DEF);		
		var newID="s"+tsfNum;				
		var newScale=$(tsf_selector).attr('scale');		
		var newRotation=$(tsf_selector).attr('rotation');		
		var newTranslation=$(tsf_selector).attr('translation');		
		var xyz=newTranslation.split(',');		
		var x=parseInt(xyz[0])+1;
		var y=parseInt(xyz[1])+1;
		var z=parseInt(xyz[2])+1;
		newTranslation=x+','+y+','+z;		
		var newNode="<"+tagName+" use='"+DEF+"'></"+tagName+">";
		var res=$(tsf_selector).after("<transform id="+newID+" onmousedown='selectmodel(this)'>"+newNode+"</transform>");

		$("transform#"+newID).attr('scale',newScale);
		$("transform#"+newID).attr('rotation',newRotation);
		$("transform#"+newID).attr('translation',newTranslation);
		var sss=$("#cn_3dant_diy").detach();
		$("#marker").after(sss);
		set_pickid(newID);
		tsfNum += 1 ;
	}
}
 * 
 */

/**
 * 更换背景
 * @param self
 */
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
/**
 * 截取Canvas显示内容
 * @param x3dID x3d标签ID
 * @returns imgUrl 图像数据
 */
function screenShot(x3dID){
	var imgUrl = document
	.getElementById(x3dID).runtime
	.getScreenshot();
	return imgUrl;
}
function getViewChange(){
	x3dom.runtime.ready = function() {	
		var viewFunc = function(evt) {
			pos = evt.position;
			var p_x=Math.round(pos.x*1000)/1000;
			var p_y=Math.round(pos.y*1000)/1000;
			var p_z=Math.round(pos.z*1000)/1000;
			pos = p_x+" "+p_y+" "+p_z;
			rot = evt.orientation;
			var r_x=Math.round(rot[0].x*1000)/1000;
			var r_y=Math.round(rot[0].y*1000)/1000;
			var r_z=Math.round(rot[0].z*1000)/1000;
			var r_a=Math.round(rot[1]*1000)/1000;
			rot = r_x+" "+r_y+" "+r_z+" "+r_a;			
		};
		$('viewpoint#v')[0].addEventListener('viewpointChanged', viewFunc, true);
	}
}
/**
 * 保存视角，
 * @param viewIDafter
 */
function saveView(viewIDafter){
	var num=Number(viewIDafter.substr(1))+1;
	var newViewID="v"+num;
	$("viewpoint#"+viewIDafter).after("<viewpoint id='"+newViewID+"' orientation='"+rot+"' position='"+pos+"'></viewpoint>");	
}
/**
 * 保存文档
 */
function saveDocument(){
	
}
/**
 * 放缩当前选择模型至适合窗口显示
 * 如果是基本模型放缩shape
 * 如果是分享的模型放缩inline
 */
function showObject(){
	var model;
	if($(tsf_selector).find("shape").length){
		model=$(tsf_selector).find("shape")[0];
	}else{
		model=$(tsf_selector).find("inline")[0];
	}
	$("x3d#model")[0].runtime.showObject(model);
}
/**
 * 重载模型
 */
function cleanScreen(){
	var sss=$("#cn_3dant_diy").detach();
	$("#marker").after(sss);
}
/**
 * 缓存
 */
function cache(){
	if(x3d.length<5){
		x3d[x3d.length] = $("scene#mainScene").html();
	}else{
		x3d[5]=$("scene#mainScene").html();
		for(var i = 0;i < 5; i++) {
			x3d[i]=x3d[i+1];
		}
	}	
}
/**
 * 恢复
 */
function recover(){
	$("scene#mainScene").html(x3d[x3d.length-1]);
	x3d.pop();
}
/**
 * jquery 提示汉化
 */
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
/*16进制颜色转为RGB格式*/ 
function colorRgb(sHex){
	var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/; 
	var sColor = sHex.toLowerCase();  
    if(sColor && reg.test(sColor)){  
        if(sColor.length === 4){  
            var sColorNew = "#";  
            for(var i=1; i<4; i+=1){  
                sColorNew += sColor.slice(i,i+1).concat(sColor.slice(i,i+1));     
            }  
            sColor = sColorNew;  
        }  
        //处理六位的颜色值  
        var sColorChange = [];  
        for(var i=1; i<7; i+=2){  
            sColorChange.push(parseInt("0x"+sColor.slice(i,i+2)));    
        } 
        for (p in sColorChange){
        	sColorChange[p]=Math.round(sColorChange[p]/0.255)/1000;
        }
        return sColorChange.join(",");  
    }else{  
        return sColor;    
    } 
}
/**
 * 添加控制按钮
 */
function addButton(){
	var id=$('#buttonID').val();
	if(!id){
		alert("请输入ID");
		return;
	}	
	var name=$('#buttonName').val();
	if(!name){
		alert("请输入按钮名称");
		return;
	}
	var size=$('#buttonSize').val();
	var kind=$('#buttonKind').val();
	var bt_code="<button id='"+id+"' type='button' class='btn "+kind+" "+size+"' aria-label='"+name+"'>"+name+"</button> ";
	$("div#control").append(bt_code);
	$('#addButtonModal').modal('hide');
}
function addScript(){
	var code=$("#script").val();
	$('#customerCode').html(code);
	$('#addScriptModal').modal('hide');
}