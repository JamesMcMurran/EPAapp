<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
    </style>
   
  </head>
  <body onload="initialize()">
  <form action="pross.php"  method="post">
	 Address of home: <input type="text" placeholder="Address, Zip or City" id="location" name="location"/><br/>
	  <input type="radio" value="cws"       name="type"/>Community WS  <br/>
    <input type="radio" value="nonTnonC"  name="type"/>Non- transient Non Community WS<br/>
    <input type="radio" value="TnonC"     name="type"/>Transient Non-community<br/>
	  <input type="submit" onclick="addToMap" name="submit"/>
  </form>
  </body>
</html>