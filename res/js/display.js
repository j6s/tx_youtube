function showMyVideos2(data) {
  var feed = data.feed;
  var entries = feed.entry || [];
  var html = ['<table id="results">'];
      html.push('<tr><td>&nbsp;</td></tr>');  
  for (var i = 0; i < entries.length; i++) {
	  var entry = entries[i];
	  var title = entry.title.$t;
      var year = entry.published.$t.substr(0,4);
	  var month = entry.published.$t.substr(5,2);
	  var day = entry.published.$t.substr(8,2);
	  var myDate=day+"."+month+"."+year;
	  var thumbnailUrl = entries[i].media$group.media$thumbnail[0].url;
	  //var playerUrl = entries[i].media$group.media$content[0].url;
	  //alert(playerUrl);
	  ///html.push('<tr><td><div class="mediaDetail" style="cursor:pointer;margin:-1px;"><div class="displayBox"><a href="'+playerUrl+'" rel="prettyPhoto" title="'+title+'"><img src="'+thumbnailUrl+'" width="94" height="54"/></a></div><div class="detailBox"><h5>'+myDate+'</h5><p><a href="'+playerUrl+'" rel="prettyPhoto">'+title+'</a></p></div></div></div></td></tr>');
	 var playerUrl = entries[i].media$group.media$content[0].url;
	 var videoId=playerUrl.substr(25,11);
	 var  playerUrl="http://www.youtube.com/watch?v="+videoId+"?rel=0";
     
     var toPush =  "<tr><td>";
	     	toPush += '<div class="mediaDetail" style="cursor:pointer;margin:-1px;">';
	     		toPush += '<div class="displayBox gallery">';
	     			toPush += '<a href="'+playerUrl+'" rel="prettyPhoto" title="'+title+'"><div class="tx_youtubevideos-thumb" style="background:url('+thumbnailUrl+');"></div></a>';
	     		toPush += '</div>';
	     		toPush += '<div class="detailBox gallery">';
	     			toPush += '<h5>'+myDate+'</h5>';
	     			toPush += '<p><a href="'+playerUrl+'"rel="prettyPhoto">'+title+'</a></p>';
	     		toPush += '</div>';
	     	toPush += '</div>';
	     toPush += '</td></tr>'


    html.push(toPush);	  
  }
  html.push('</table>');
  document.getElementById('innerBox').innerHTML = html.join('');
  if (entries.length > 0) {
    loadVideo(entries[0].media$group.media$content[0].url, false);
  }
}



