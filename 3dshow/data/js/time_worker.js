var i=0;
var t;
onmessage = function(event) {
	var data=String(event.data);
    if(event.data==="start"){
    	timedCount();
    }
    if(event.data==="reset"){
    	clearTimeout(t);
    	i=0;
    }    
};
function timedCount()
{
		i=i+1;
		postMessage(i);
		t = setTimeout("timedCount()",500);	
}