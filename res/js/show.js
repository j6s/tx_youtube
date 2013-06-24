function showMyVideos2(data) {
  var feed = data.feed;
  var entries = feed.entry || [];
  var html = ['<ul class="videos" >'];
  var videoNumber=entries.length;

   videoNumber=entries.length;
 
   for (var i = 0; i <videoNumber; i++) {
    var entry = entries[i];
    var title = entry.title.$t;
	 var year = entry.published.$t.substr(0,4);
	 var month = entry.published.$t.substr(5,2);
	 var day = entry.published.$t.substr(8,2);
	   var myDate=day+"."+month+"."+year;
	   
	var thumbnailUrl = entries[i].media$group.media$thumbnail[0].url;
    var playerUrl = entries[i].media$group.media$content[0].url;
	var videoId=playerUrl.substr(25,11);
	if(i==0)
	{
		loadVideo(videoId);
	}
    html.push('<li style="cursor:pointer" class="mediaDetail" onclick="loadVideo(\''+videoId+'\')">',
              '<div class="displayBox"><img src="', 
              thumbnailUrl, '" width="94" height="54"/></div><span class="detailBox"><h5>',myDate,'</h5><p><a>',title,'</a></p></span>', '</span></li>');
  }
  html.push('</ul><br style="clear: left;"/>');
  document.getElementById('videos2').innerHTML = html.join('');
  if (entries.length > 0) {
    //loadVideo(entries[0].media$group.media$content[0].url, false);
  }
}
