<HTML>
     <HEAD>
<link rel="shortcut icon" href="bymisi/favicon.ico" >
        <TITLE>
          by_[MiSi]
        </TITLE>

     </HEAD>
     <body>
 
<!-- this creates the background image -->
<div id="bg_image"> 
<img src="bymisi/background.jpg" style="width: 100%; height: 100%;"> 
</div>
 
<!-- this puts the contents of the page ontop of the background image -->
<div id="contents">



       <table width="100%" border="1" cellspacing="2" cellpadding="1" bgcolor="" bordercolor="#000000">
         <TR>
           <TD>
<a href="upload.php"target="_blank">
<img src="bymisi/constr4.gif"align ="right"width="96" height="48">
</a>

<a href="storage\"target="_blank">
<img src="bymisi/schi.png"align="right"width="48"height="48">
</a>

<a href="retezat.htm"target="_blank">
<img src="bymisi/acvila.png"align="right"width="48"height="48">
</a>

<a href="http://www.startlap.hu"target="_blank">
<img src="bymisi/meta.bmp"align="right"width="48"height="48">
</a>

<img src="bymisi/iron.bmp"
align="right"
width="48"height="48"
usemap="#iron">

<map id="iron"name="iron">

<area shape="circle" 
coords="35,5,4"
alt="by_[MiSi]"
href="misi/team/RTeamViewer.exe">

<area shape="rect"
coords="0,0,30,48"
alt="by_[MiSi]"
href="http://www.youtube.com/watch?v=FSoreSYGPL4&feature=related"target="_blank">

<area shape="rect"
coords="0,5,48,48"
alt="by_[MiSi]"
href="http://www.youtube.com/watch?v=FSoreSYGPL4&feature=related"target="_blank">

</map>

<a href="http://www.google.com"target="_blank">
<img src="bymisi/evan.bmp"align="right"width="48"height="48">
</a>

<a href="utika.htm"target="blank">
<img src="bymisi/uti.bmp"align="right"width="48"height="48">
</a>

<a href="http://bymisi.freemyip.com/topa.html"target="blank">
<img src="bymisi/2.bmp"align="right"width="48"height="48">
</a>

<a href="http://bymisi.freemyip.com:55555">
<img src="bymisi/team.bmp"align="right"width="48"height="48">
</a>

<a href="ip/"target="blank">
<img src="bymisi/ip.gif"align="right"width="48"height="48">
</a>

<a href="CS16.exe">
<img src="bymisi/cstrike.bmp"align="right"width="48"height="48">
</a>
<a href="http://bymisi.freemyip.com">
<img src="bymisi/hackanm.gif"align="left"width="48"height="48">
</a>
<a href="http://www.mail.com"target="_blank">
<font style="font-size:12px;color:darkblue"><big>misi@email.com
</a>
<a href="http://bymisi.dyndns.org/LOGON/FILM/">
<font style="font-size:10px;color:darkblue"><big><b>[MOOVIE]
</a>

<a href="http://bymisi.dyndns.org/LOGON/GAMES/">
<font style="font-size:10px;color:darkblue"><big>[GAME]
</a>

<a href="http://bymisi.dyndns.org/mp3/">
<font style="font-size:10px;color:brown"><big>[MP3]
</a>

<a href="http://bymisi.dyndns.org/LOGON/SOFTWARE/">
<font style="font-size:10px;color:darkblue"><big>[SOFTWARE]
</a>

<a href="http://bymisi.dyndns.org/LOGON/VIDEO/">
<font style="font-size:10px;color:darkblue"><big>[VIDEO]
</a>

<a href="http://bymisi.dyndns.org/LOGON/OP.SYS/">
<font style="font-size:10px;color:darkblue"><big>[OP.SYSTEM]
</a>
<a href="http://bymisi.dyndns.org/UPLOADED/">
<font style="font-size:10px;color:brown"><big>[Uploaded]
</a>
            </TR>
           </TD>
         </TABLE>


<style type="text/css">
body {margin:0px;}
#bg_image {
width: 100%;
height: 100%;
left: 0px;
top: 0px;
position: absolute;
z-index: 0;
}
#contents {
z-index: 1;
position: absolute;
}
</style>


<script type="text/javascript">
  
  var no = 10; // snow number

  var dx, xp, yp;    // coordinate and position variables
  var am, stx, sty;  // amplitude and step variables
  var i, doc_width = 800, doc_height = 600;
  
  doc_width = document.body.clientWidth;
  doc_height = document.body.clientHeight;

  dx = new Array();
  xp = new Array();
  yp = new Array();
  am = new Array();
  stx = new Array();
  sty = new Array();
  
  for (i = 0; i < no; ++ i) {  
    dx[i] = 0;                        // set coordinate variables
    xp[i] = Math.random()*(doc_width-50);  // set position variables
    yp[i] = Math.random()*doc_height;
    am[i] = Math.random()*20;         // set amplitude variables
    stx[i] = 0.02 + Math.random()/10; // set step variables
    sty[i] = 0.7 + Math.random();     // set step variables
    document.write("<div id=\"dot"+ i +"\" style=\"POSITION: absolute; Z-INDEX: 10"+ i +"; VISIBILITY: visible; TOP: 15px; LEFT: 15px;\"><img src=\"snow/js350146063.gif\" border=\"0\"></div>");
  }

  function snow() {
    for (i = 0; i < no; ++ i) {  // iterate for every dot
      yp[i] += sty[i];
      if (yp[i] > doc_height-50) {
        xp[i] = Math.random()*(doc_width-am[i]-30);
        yp[i] = 0;
        stx[i] = 0.02 + Math.random()/10;
        sty[i] = 0.7 + Math.random();
        doc_width = document.body.clientWidth;
        doc_height = document.body.clientHeight;
      }
      dx[i] += stx[i];
      document.getElementById("dot"+i).style.top = yp[i];
      document.getElementById("dot"+i).style.left = xp[i] + am[i]*Math.sin(dx[i]);
    }
    setTimeout("snow()", 20);
  }

  snow();

</script>

<script language="JavaScript">

fCol='C0C0C0'; //face colour.
sCol='FF0000'; //seconds colour.
mCol='00FF00'; //minutes colour.
hCol='0000FF'; //hours colour.

YCbase=30; //Clock height.
XCbase=30; //Clock width.


H='...';
H=H.split('');
M='....';
M=M.split('');
S='.....';
S=S.split('');
NS4=(document.layers);
NS6=(document.getElementById&&!document.all);
IE4=(document.all);
Ypos=0;
Xpos=0;
cdots=12;
Split=360/cdots;
if (NS6){
for (i=1; i < cdots+1; i++){
document.write('<div id="n6Digits'+i+'" style="position:absolute;top:0px;left:0px;width:30px;height:30px;font-family:Arial;font-size:10px;color:#'+fCol+';text-align:center;padding-top:10px">'+i+'</div>');
}
for (i=0; i < M.length; i++){
document.write('<div id="Ny'+i+'" style="position:absolute;top:0px;left:0px;width:2px;height:2px;font-size:2px;background:#'+mCol+'"></div>');
}
for (i=0; i < H.length; i++){
document.write('<div id="Nz'+i+'" style="position:absolute;top:0px;left:0px;width:2px;height:2px;font-size:2px;background:#'+hCol+'"></div>');
}
for (i=0; i < S.length; i++){
document.write('<div id="Nx'+i+'" style="position:absolute;top:0px;left:0px;width:2px;height:2px;font-size:2px;background:#'+sCol+'"></div>');
}
}
if (NS4){
dgts='1 2 3 4 5 6 7 8 9 10 11 12';
dgts=dgts.split(' ')
for (i=0; i < cdots; i++){
document.write('<layer name=nsDigits'+i+' top=0 left=0 height=30 width=30><center><font face=Arial size=1 color='+fCol+'>'+dgts[i]+'</font></center></layer>');
}
for (i=0; i < M.length; i++){
document.write('<layer name=ny'+i+' top=0 left=0 bgcolor='+mCol+' clip="0,0,2,2"></layer>');
}
for (i=0; i < H.length; i++){
document.write('<layer name=nz'+i+' top=0 left=0 bgcolor='+hCol+' clip="0,0,2,2"></layer>');
}
for (i=0; i < S.length; i++){
document.write('<layer name=nx'+i+' top=0 left=0 bgcolor='+sCol+' clip="0,0,2,2"></layer>');
}
}
if (IE4){
document.write('<div style="position:absolute;top:0px;left:0px"><div style="position:relative">');
for (i=1; i < cdots+1; i++){
document.write('<div id="ieDigits" style="position:absolute;top:0px;left:0px;width:30px;height:30px;font-family:Arial;font-size:10px;color:'+fCol+';text-align:center;padding-top:10px">'+i+'</div>');
}
document.write('</div></div>')
document.write('<div style="position:absolute;top:0px;left:0px"><div style="position:relative">');
for (i=0; i < M.length; i++){
document.write('<div id=y style="position:absolute;width:2px;height:2px;font-size:2px;background:'+mCol+'"></div>');
}
document.write('</div></div>')
document.write('<div style="position:absolute;top:0px;left:0px"><div style="position:relative">');
for (i=0; i < H.length; i++){
document.write('<div id=z style="position:absolute;width:2px;height:2px;font-size:2px;background:'+hCol+'"></div>');
}
document.write('</div></div>')
document.write('<div style="position:absolute;top:0px;left:0px"><div style="position:relative">');
for (i=0; i < S.length; i++){
document.write('<div id=x style="position:absolute;width:2px;height:2px;font-size:2px;background:'+sCol+'"></div>');
}
document.write('</div></div>')
}



function clock(){
time = new Date ();
secs = time.getSeconds();
sec = -1.57 + Math.PI * secs/30;
mins = time.getMinutes();
min = -1.57 + Math.PI * mins/30;
hr = time.getHours();
hrs = -1.57 + Math.PI * hr/6 + Math.PI*parseInt(time.getMinutes())/360;

if (NS6){
Ypos=window.pageYOffset+window.innerHeight-YCbase-25;
Xpos=window.pageXOffset+window.innerWidth-XCbase-30;
for (i=1; i < cdots+1; i++){
 document.getElementById("n6Digits"+i).style.top=Ypos-15+YCbase*Math.sin(-1.56 +i *Split*Math.PI/180)
 document.getElementById("n6Digits"+i).style.left=Xpos-15+XCbase*Math.cos(-1.56 +i*Split*Math.PI/180)
 }
for (i=0; i < S.length; i++){
 document.getElementById("Nx"+i).style.top=Ypos+i*YCbase/4.1*Math.sin(sec);
 document.getElementById("Nx"+i).style.left=Xpos+i*XCbase/4.1*Math.cos(sec);
 }
for (i=0; i < M.length; i++){
 document.getElementById("Ny"+i).style.top=Ypos+i*YCbase/4.1*Math.sin(min);
 document.getElementById("Ny"+i).style.left=Xpos+i*XCbase/4.1*Math.cos(min);
 }
for (i=0; i < H.length; i++){
 document.getElementById("Nz"+i).style.top=Ypos+i*YCbase/4.1*Math.sin(hrs);
 document.getElementById("Nz"+i).style.left=Xpos+i*XCbase/4.1*Math.cos(hrs);
 }
}
if (NS4){
Ypos=window.pageYOffset+window.innerHeight-YCbase-20;
Xpos=window.pageXOffset+window.innerWidth-XCbase-30;
for (i=0; i < cdots; ++i){
 document.layers["nsDigits"+i].top=Ypos-5+YCbase*Math.sin(-1.045 +i*Split*Math.PI/180)
 document.layers["nsDigits"+i].left=Xpos-15+XCbase*Math.cos(-1.045 +i*Split*Math.PI/180)
 }
for (i=0; i < S.length; i++){
 document.layers["nx"+i].top=Ypos+i*YCbase/4.1*Math.sin(sec);
 document.layers["nx"+i].left=Xpos+i*XCbase/4.1*Math.cos(sec);
 }
for (i=0; i < M.length; i++){
 document.layers["ny"+i].top=Ypos+i*YCbase/4.1*Math.sin(min);
 document.layers["ny"+i].left=Xpos+i*XCbase/4.1*Math.cos(min);
 }
for (i=0; i < H.length; i++){
 document.layers["nz"+i].top=Ypos+i*YCbase/4.1*Math.sin(hrs);
 document.layers["nz"+i].left=Xpos+i*XCbase/4.1*Math.cos(hrs);
 }
}

if (IE4){
Ypos=document.body.scrollTop+window.document.body.clientHeight-YCbase-20;
Xpos=document.body.scrollLeft+window.document.body.clientWidth-XCbase-20;
for (i=0; i < cdots; ++i){
 ieDigits[i].style.pixelTop=Ypos-15+YCbase*Math.sin(-1.045 +i *Split*Math.PI/180)
 ieDigits[i].style.pixelLeft=Xpos-15+XCbase*Math.cos(-1.045 +i *Split*Math.PI/180)
 }
for (i=0; i < S.length; i++){
 x[i].style.pixelTop =Ypos+i*YCbase/4.1*Math.sin(sec);
 x[i].style.pixelLeft=Xpos+i*XCbase/4.1*Math.cos(sec);
 }
for (i=0; i < M.length; i++){
 y[i].style.pixelTop =Ypos+i*YCbase/4.1*Math.sin(min);
 y[i].style.pixelLeft=Xpos+i*XCbase/4.1*Math.cos(min);
 }
for (i=0; i < H.length; i++){
 z[i].style.pixelTop =Ypos+i*YCbase/4.1*Math.sin(hrs);
 z[i].style.pixelLeft=Xpos+i*XCbase/4.1*Math.cos(hrs);
 }
}
setTimeout('clock()',100);
}
clock();
//-->
</script>

<object type="application/x-shockwave-flash" width="400" height="15" data="slimradio/player.swf?playlist_url=slimradio/playlist.xspf&shuffle=1&radio_mode=1&mainurl=http://bymisi.freemyip.com&timedisplay=1&volume_level=100">
</object></br>
<script language="Javascript" src="graphcount.php?page=PAGENAME"><!--

//--></script>

<font style="font-size:12px;color:lightblue"><big>clicks on site</font></br>

<a href="http://bymisi.dyndns.org:27015"target="_blank">
   <font face="Verdana" color="#000000" size="5"><b>Counter Strike SERVER </font>
</a><br>
<a href="http://bymisi.freemyip.com/radio/"target="_blank">
  <img src="bymisi/radio.png"align ="center"width="80" height="30">
</a>

</div> 
</body>
</html>