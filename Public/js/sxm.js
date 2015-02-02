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
var tsfNum;
var pickID;
var tsf_selector;
function set_pickid(id){
	pickID=id;
	tsf_selector="transform#"+pickID;
}
function form_disable(){	
	$("[onchange='change()']").attr("disabled","disabled");	
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
}
function selectmodel(tsf)
{
	set_pickid($(tsf).attr("id"));
	set_form();
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
}
function change_point(){
	var radius=$("#radius").val();
	var sc=radius+","+radius+","+radius;
	$("#marker").attr("scale",sc);
}
function groupClick(event)
{
	var info=event.hitPnt;
	$('#marker').attr('translation', info);
	var xyz="X:"+info[0]+"<br/>Y:"+info[1]+"<br/>Z:"+info[2];
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
	pickID=null;
	set_form();
	//TODO:删除后变更表格状态
}
function emptymodel(){
	if(!confirm("确定移除所以模型？")){
		return false;
	}
	$("group").empty();	
	tsfNum=0;
	pickID=null;
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
	  var str="<transform id='s"+tsfNum+"' onclick='selectmodel(this)' rotation='0,0,1,0' scale='1,1,1' translation='0,0,0'><Inline nameSpaceName='in' mapDEFToID='true' url='"+lujing+"/"+daima+"/model.x3d'></Inline></transform>";
		$('group').append(str);	
		set_pickid("s"+tsfNum);				
		set_form();
		tsfNum++;
		$('#addmodel').modal('hide');
}