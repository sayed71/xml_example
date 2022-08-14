<?php    
// Send the headers
header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
mysql_connect("localhost","root","");
$db=isset($_REQUEST["d"]) ? $_REQUEST["d"] : "world";
mysql_select_db($db);
$sql=isset($_REQUEST["s"]) ? $_REQUEST["s"] : "";
function Makexsl( $sql)
{
	$out="";
	if ($sql=="") die('Argument Query Missing');
	$out .= "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
	$out .= "<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\n";
	$out .= "<xsl:template match=\"/\">\n";
	$out .= "<html>\n";
	$out .= "<body>\n";
	$out .= '<h2 align="center">'.$sql."</h2>\n";
	$out .= "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" bgcolor=\"#D0D0D0\" align=\"center\">\n";
	
		$rs=mysql_query($sql);
		$i = 0;
		$td="";
		$th="";
		while ($i < mysql_num_fields($rs)) 
		{
		   $meta = mysql_fetch_field($rs, $i++);
		   $td .= "\t\t<td bgcolor=\"#FFFFFF\"><xsl:value-of select=\"".$meta->name."\"/></td>\n";
		   $th .= "\t\t<th bgcolor=\"#000000\" style=\"color:#FFF;\">".strtoupper($meta->name)."</th>\n";
		}
		
		$out .= "\t<tr>\n";
		$out .= $th;
		$out .= "\t</tr>\n";
		$out .= "\t<xsl:for-each select=\"xml/Record\">\n";
		$out .= "\t<tr>\n";
		$out .= $td;
		$out .= "\t</tr>\n";
		$out .= "\t</xsl:for-each>\n";
	
	$out .= "</table>\n";
	$out .= "</body>\n";
	$out .= "</html>\n";
	$out .= "</xsl:template>\n";
	$out .= "</xsl:stylesheet>\n";
	$fp=fopen("xslQuery.xsl","w");
	fwrite($fp,$out);
	fclose($fp);	
}
Makexsl($sql);
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<?xml-stylesheet type=\"text/xsl\" href=\"xslQuery.xsl\"?>\n";
echo "<xml>\n";

// echo some dynamically generated content here

if($sql!="")
{
	$rs=mysql_query($sql);
	$i = 0;
	while ($i < mysql_num_fields($rs)) 
	{
       $meta = mysql_fetch_field($rs, $i);
	   $fld[$i]=$meta->name;
    $i++;
	}
	while($r=mysql_fetch_row($rs))
	{
		echo "\t<Record>\n";
		for($j=0;$j<$i;$j++)
		echo "\t\t<".$fld[$j].">".htmlspecialchars($r[$j])."</".$fld[$j].">\n";
		echo "\t</Record>\n";
	}
}

echo "</xml>\n";
mysql_free_result($rs);

?>