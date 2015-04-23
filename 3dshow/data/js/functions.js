// JavaScript Document
var activeID = 0;
var actThumb = 0;
							
function initThumbs()
{
	for(var i=0; i<5; i++) {
		if(i == 0)
			document.getElementById("Thumb"+i).style.backgroundImage = "url(data/thumbs/" + modelData[i][0] + "_Over.png)";
		else
			document.getElementById("Thumb"+i).style.backgroundImage = "url(data/thumbs/" + modelData[i][0] + ".png)";
	}
}

function mouseOver(id)
{
	if((id + actThumb) != activeID)
	{
		document.getElementById("Thumb"+id).style.backgroundImage = "url(data/thumbs/" + modelData[id+actThumb][0] + "_Over.png)";
	}
	document.getElementById("HoverInfo").innerHTML = modelData[id+actThumb][1];
	document.getElementById("HoverInfo").style.display = "block";
}

function mouseOut(id)
{
	if((id + actThumb) != activeID)
	{	
		document.getElementById("Thumb"+id).style.backgroundImage = "url(data/thumbs/" + modelData[id+actThumb][0] + ".png)";
	}
	document.getElementById("HoverInfo").style.display = "none";
}

function mouseMove(event)
{
	document.getElementById("HoverInfo").style.left = event.clientX + 20 +"px";
	document.getElementById("HoverInfo").style.top  = event.clientY - 3 + "px";
}

function clickThumb(id)
{
	for(var i=0; i<5; i++)
	{
		document.getElementById("Thumb"+i).style.backgroundImage = "url(data/thumbs/" + modelData[actThumb+i][0] + ".png)";
	}
	activeID = id+actThumb;
	document.getElementById("Thumb"+id).style.backgroundImage = "url(data/thumbs/" + modelData[id+actThumb][0] + "_Over.png)";
	
	document.getElementById("Content3D").src = "data/models/" + modelData[id+actThumb][0] + ".html";
	document.getElementById("Name").innerHTML = modelData[id+actThumb][1];
	document.getElementById("Place").innerHTML = (modelData[id+actThumb][2]).replace(/;/g, "<br>");
	document.getElementById("Date").innerHTML = modelData[id+actThumb][3];
	document.getElementById("Artist").innerHTML = (modelData[id+actThumb][4]).replace(/;/g, "<br>");
	document.getElementById("Material").innerHTML = modelData[id+actThumb][5];
	document.getElementById("Number").innerHTML = modelData[id+actThumb][6];
	document.getElementById("Location").innerHTML = modelData[id+actThumb][7];
}

function prevThumb()
{
	if(actThumb > 0)
	{
		actThumb--;
		for(var i=0; i<5; i++) {
			if( (actThumb+i) == activeID )
				document.getElementById("Thumb"+i).style.backgroundImage = "url(data/thumbs/" + modelData[actThumb+i][0] + "_Over.png)";
			else
				document.getElementById("Thumb"+i).style.backgroundImage = "url(data/thumbs/" + modelData[actThumb+i][0] + ".png)";
		}
	}
}

function nextThumb()
{
	if(actThumb+5 < modelData.length)
	{
		actThumb++;
		for(var i=0; i<5; i++) {
			if( (actThumb+i) == activeID )
				document.getElementById("Thumb"+i).style.backgroundImage = "url(data/thumbs/" + modelData[actThumb+i][0] + "_Over.png)";
			else
				document.getElementById("Thumb"+i).style.backgroundImage = "url(data/thumbs/" + modelData[actThumb+i][0] + ".png)";
		}
	}
}